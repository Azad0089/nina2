<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function advertising()
    {
        return view('pages.advertising');
    }

    public function premium()
    {
        return view('pages.premium');
    }

    public function support()
    {
        return view('pages.support');
    }

    public function terms()
    {
        return view('pages.content-page', [
            'title' => 'Terms of Service',
            'lead' => 'Rules and conditions governing use of Nina.Sucks.',
            'color' => '#ff2bd6',
            'bodyHtml' => '<h2>1. Acceptance of Terms</h2><p>By creating an account or accessing Nina.Sucks, you certify that you are at least 18 years old and agree to adhere to these terms.</p><h2>2. Prohibited Content</h2><p>Non-consensual content, underage content, and harassment are strictly prohibited and will lead to account termination.</p>'
        ]);
    }

    public function privacy()
    {
        return view('pages.content-page', [
            'title' => 'Privacy Policy',
            'lead' => 'How we handle and protect member data.',
            'color' => '#19e3ff',
            'bodyHtml' => '<h2>1. Information We Collect</h2><p>We collect username, email, and authentication details necessary to host your blog securely.</p><h2>2. Cookies and Sessions</h2><p>Essential cookies maintain session continuity and safety tokens.</p>'
        ]);
    }

    public function rules()
    {
        return view('pages.content-page', [
            'title' => 'Community Rules',
            'lead' => 'Guidelines for keeping Nina.Sucks safe, respectful, and vibrant.',
            'color' => '#ffd60a',
            'bodyHtml' => '<h2>Content Standards</h2><ul><li>All creators must be 18+.</li><li>Properly tag content to assist member exploration.</li><li>Respect intellectual property and fellow creators.</li></ul>'
        ]);
    }

    public function dmca()
    {
        return view('pages.content-page', [
            'title' => 'DMCA & Copyright Notice',
            'lead' => 'Designated copyright agent and takedown procedure.',
            'color' => '#ff1f4b',
            'bodyHtml' => '<h2>Filing a Notice</h2><p>Copyright owners can submit takedown notices via our contact desk with valid proof of ownership.</p>'
        ]);
    }

    public function cookies()
    {
        return view('pages.content-page', [
            'title' => 'Cookie Policy',
            'lead' => 'Details on cookie usage across the application.',
            'color' => '#19e3ff',
            'bodyHtml' => '<h2>Strictly Necessary Cookies</h2><p>Session cookies are utilized to authenticate logins and preserve security across requests.</p>'
        ]);
    }

    public function about()
    {
        return view('pages.content-page', [
            'title' => 'About ' . site_name(),
            'lead' => 'Decentralized expression, unlimited creative blogs, and pure adult microblogging.',
            'color' => '#ff2bd6',
            'bodyHtml' => '<h2>Our Philosophy</h2><p>' . site_name() . ' was conceived as a vibrant home for unfiltered adult self-expression, photography, literature, and visual culture. Unlike sanitized corporate platforms governed by opaque algorithms, ' . site_name() . ' gives creators complete ownership of their content, customizable appearance, and an open reverse-chronological feed.</p><h2>Unlimited Blogs for Creators</h2><p>Every account holder can create unlimited independent blogs for distinct personas, projects, or genres. Customize accent palettes, banners, avatar artwork, and bio external links.</p><h2>Creator Economy & Community</h2><p>Earn reward points simply by sharing, following, and referring peers. Boost your creations in our fair community ad marketplace or verify your identity for life.</p>'
        ]);
    }

    public function faq()
    {
        return view('pages.content-page', [
            'title' => 'Frequently Asked Questions',
            'lead' => 'Everything you need to know about blogging, points, and platform features.',
            'color' => '#19e3ff',
            'bodyHtml' => '<h2>How do multiple blogs work?</h2><p>From your Creator Studio dashboard under "My Blogs", you can spin up unlimited additional blogs at any time. Each blog receives its own unique web address, title, theme color, avatar, and post archive.</p><h2>How do reward points work?</h2><p>You earn reward points by referring friends (100 points per referral) and through active community participation. Points can be spent in the Advertising Marketplace to boost your posts to thousands of members.</p><h2>How does the post queue work?</h2><p>The post queue lets you schedule up to 20 text, photo, quote, and link posts. Queued posts are scheduled and automatically published sequentially.</p><h2>What are the ID Verified Creator badges?</h2><p>Creators who submit proof of legal age and identity receive the permanent green ID Verified badge, unlocking boosted discovery and reader trust.</p>'
        ]);
    }

    public function compliance()
    {
        return view('pages.content-page', [
            'title' => '18 U.S.C. § 2257 Recordkeeping Compliance',
            'lead' => 'Mandatory age verification, operator notice, and legal recordkeeping statement.',
            'color' => '#ffd60a',
            'bodyHtml' => '<h2>18 U.S.C. 2257 Notice</h2><p>All visual depictions, images, and video clips displayed on ' . site_name() . ' depicting actual sexually explicit conduct are produced, uploaded, and maintained in compliance with the provisions of Title 18 U.S.C. Section 2257 and Section 2257A, as well as Part 75 of Title 28 of the Code of Federal Regulations.</p><h2>Custodian of Records</h2><p>The primary records custodian maintains records containing the primary identification documents verifying that all performers and content contributors depicted were at least 18 years of age at the time of content creation.</p><h2>Exemption Statement</h2><p>Certain content displayed may consist of third-party user generated content, textual descriptions, simulated depictions, or non-record keeping compliant exemptions as recognized under federal statute.</p>'
        ]);
    }

    public function safety()
    {
        return view('pages.content-page', [
            'title' => 'Trust, Safety & Guidelines',
            'lead' => 'Our zero-tolerance standards for consent, age compliance, and creator protection.',
            'color' => '#ff1f4b',
            'bodyHtml' => '<h2>Zero Tolerance Policies</h2><p>We enforce strict, non-negotiable zero tolerance for non-consensual content, CSAM/underage material, doxxing, harassment, and unauthorized distribution. Any violations result in immediate permanent account termination and referral to legal authorities.</p><h2>Reporting Violations</h2><p>Every post includes a quick flag/report button. Our moderation team monitors reports continuously to ensure platform integrity and creator safety.</p>'
        ]);
    }

    public function forgotView()
    {
        return view('pages.content-page', [
            'title' => 'Password Recovery',
            'lead' => 'Reset your password and regain access to your blogs.',
            'color' => '#19e3ff',
            'bodyHtml' => '<div style="max-width:440px;margin:20px auto;background:rgba(255,255,255,0.03);padding:24px;border:1px solid var(--line);border-radius:8px;"><p style="margin-bottom:16px;">Enter your registered account email and we will send a password reset token.</p><form method="POST" action="' . route('contact') . '"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="topic" value="Password Reset Request"><div style="margin-bottom:14px;"><label style="display:block;font-size:13px;margin-bottom:6px;color:var(--mute);">Registered Email Address</label><input type="email" name="email" required placeholder="you@example.com" style="width:100%;padding:10px;background:var(--ink);border:1px solid var(--line);color:var(--text);border-radius:4px;"></div><input type="hidden" name="name" value="Password Recovery User"><input type="hidden" name="message" value="User requested password reset link."><button type="submit" class="btn btn-fill" style="width:100%;">Send Recovery Instructions</button></form></div>'
        ]);
    }

    public function contactView(Request $request)
    {
        return view('pages.content-page', [
            'title' => 'Contact Us',
            'lead' => 'Reach our team directly for support, verifications, or queries.',
            'color' => '#19e3ff',
            'isContact' => true,
            'bodyHtml' => '<p>Please fill out the form below and our support team will respond promptly.</p>'
        ]);
    }
}
