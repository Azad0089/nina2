<?php

namespace App\Http\Controllers;

use App\Models\ClassifiedAd;
use App\Models\Message;
use App\Models\Report;
use App\Models\Setting;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClassifiedController extends Controller
{
    public function index(Request $request)
    {
        if (!classifieds_enabled()) {
            abort(404);
        }
        ensure_platform_tables();
        $this->expireOld();
        $perPage = max(12, min(96, (int) Setting::get('classifieds_per_page', 36)));
        $maxTags = max(1, (int) Setting::get('classifieds_max_tags', 30));
        $tag = strtolower(trim(ltrim((string) $request->query('tag', ''), '#')));

        $query = ClassifiedAd::live()->orderByDesc('id');
        if ($tag !== '') {
            $query->where('tags', 'like', '%"' . $tag . '"%');
        }
        $ads = $query->paginate($perPage)->withQueryString();

        $tagCounts = [];
        ClassifiedAd::live()->get(['tags'])->each(function ($ad) use (&$tagCounts) {
            foreach ((array) $ad->tags as $item) {
                $clean = strtolower(trim((string) $item));
                if ($clean !== '') {
                    $tagCounts[$clean] = ($tagCounts[$clean] ?? 0) + 1;
                }
            }
        });
        arsort($tagCounts);
        $tagCounts = array_slice($tagCounts, 0, $maxTags, true);

        return view('classifieds.index', [
            'ads' => $ads,
            'tagCounts' => $tagCounts,
            'tag' => $tag,
            'active_nav' => 'classifieds',
        ]);
    }

    public function show(int $id)
    {
        if (!classifieds_enabled()) {
            abort(404);
        }
        ensure_platform_tables();
        $this->expireOld();
        $ad = ClassifiedAd::live()->findOrFail($id);
        return view('classifieds.show', [
            'ad' => $ad,
            'active_nav' => 'classifieds',
        ]);
    }

    public function message(Request $request, int $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sign in to message the person who posted this ad.');
        }
        ensure_platform_tables();
        ensure_private_message_tables();
        $ad = ClassifiedAd::live()->findOrFail($id);
        if ((int) $ad->user_id === (int) Auth::id()) {
            return back()->with('error', 'This is your own ad.');
        }
        $owner = \App\Models\User::find($ad->user_id);
        if (!$owner) {
            return back()->with('error', 'This ad cannot be contacted.');
        }
        if (Auth::user()->hasBlocked($owner->id) || Auth::user()->isBlockedBy($owner->id)) {
            return back()->with('error', 'This message cannot be sent.');
        }
        $note = trim((string) $request->input('body'));
        if ($note === '') {
            return back()->with('error', 'Write a message first.');
        }
        $body = "This message is about the classified ad: " . $ad->title . "\n\n" . $note;
        Message::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $ad->user_id,
            'subject' => 'Classified ad: ' . Str::limit($ad->title, 120, ''),
            'body' => mb_substr($body, 0, 3000),
            'is_read' => false,
        ]);
        return back()->with('success', 'Your private message was sent.');
    }

    public function report(Request $request, int $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $ad = ClassifiedAd::findOrFail($id);
        Report::create([
            'user_id' => Auth::id(),
            'target_type' => 'classified',
            'target_id' => (string) $ad->id,
            'reason' => $request->input('reason', 'Classified ad'),
            'details' => $request->input('details'),
            'status' => 'open',
        ]);
        return back()->with('success', 'Report sent to the admin.');
    }

    public function manage()
    {
        ensure_platform_tables();
        $ads = ClassifiedAd::where('user_id', Auth::id())->orderByDesc('id')->get();
        return view('dashboard', [
            'section' => 'classifieds',
            'user' => Auth::user(),
            'blog' => Auth::user()->mainBlog,
            'myAds' => $ads,
            'classifiedLimits' => $this->limits(),
        ]);
    }

    public function store(Request $request)
    {
        ensure_platform_tables();
        $data = $this->payload($request);
        $months = max(1, (int) Setting::get('classifieds_expire_months', 12));
        $ad = ClassifiedAd::create($data + [
            'user_id' => Auth::id(),
            'status' => 'published',
            'expires_at' => now()->addMonths($months),
        ]);
        return redirect()->route('classifieds.show', $ad->id)->with('success', 'Classified ad published.');
    }

    public function update(Request $request, int $id)
    {
        $ad = ClassifiedAd::where('user_id', Auth::id())->findOrFail($id);
        $ad->fill($this->payload($request, $ad))->save();
        return back()->with('success', 'Classified ad updated.');
    }

    public function destroy(int $id)
    {
        ClassifiedAd::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Classified ad deleted.');
    }

    private function fail(string $message): void
    {
        throw new HttpResponseException(redirect()->back()->withInput()->with('error', $message));
    }

    private function expireOld(): void
    {
        ClassifiedAd::where('status', 'published')->whereNotNull('expires_at')->where('expires_at', '<=', now())->update(['status' => 'expired']);
    }

    private function limits(): array
    {
        return [
            'title_min' => max(3, (int) Setting::get('classifieds_title_min', 10)),
            'title_max' => max(10, (int) Setting::get('classifieds_title_max', 80)),
            'body_min' => max(10, (int) Setting::get('classifieds_body_min', 30)),
            'body_max' => max(50, (int) Setting::get('classifieds_body_max', 5000)),
            'tags_min' => max(1, (int) Setting::get('classifieds_tags_min', 2)),
            'tags_max' => max(2, (int) Setting::get('classifieds_tags_max', 6)),
        ];
    }

    private function payload(Request $request, ?ClassifiedAd $existing = null): array
    {
        $limits = $this->limits();
        $tags = array_values(array_unique(array_filter(array_map(function ($tag) {
            return strtolower(trim(ltrim((string) $tag, '#')));
        }, preg_split('/[,#]+/', (string) $request->input('tags', ''))))));
        $title = trim((string) $request->input('title'));
        $description = trim((string) $request->input('description'));
        if (mb_strlen($title) < $limits['title_min'] || mb_strlen($title) > $limits['title_max']) {
            $this->fail('Title must be ' . $limits['title_min'] . ' to ' . $limits['title_max'] . ' characters.');
        }
        if (mb_strlen($description) < $limits['body_min'] || mb_strlen($description) > $limits['body_max']) {
            $this->fail('Description must be ' . $limits['body_min'] . ' to ' . $limits['body_max'] . ' characters.');
        }
        if (count($tags) < $limits['tags_min'] || count($tags) > $limits['tags_max']) {
            $this->fail('Use ' . $limits['tags_min'] . ' to ' . $limits['tags_max'] . ' tags.');
        }
        $data = [
            'title' => $title,
            'description' => $description,
            'tags' => $tags,
            'price' => trim((string) $request->input('price')) ?: null,
            'contact_email' => trim((string) $request->input('contact_email')) ?: null,
            'phone' => trim((string) $request->input('phone')) ?: null,
            'location' => trim((string) $request->input('location')) ?: null,
        ];
        foreach (['image_one' => 'image_one', 'image_two' => 'image_two'] as $input => $column) {
            if ($request->hasFile($input)) {
                $file = $request->file($input);
                $name = 'ad_' . Auth::id() . '_' . time() . '_' . Str::random(4) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads', $name, 'public');
                $dir = public_path('uploads');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                @copy($file->getRealPath(), $dir . '/' . $name);
                $data[$column] = 'uploads/' . $name;
            } elseif ($existing) {
                $data[$column] = $existing->{$column};
            }
        }
        return $data;
    }
}
