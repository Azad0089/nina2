<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'subject',
        'body',
        'is_read',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'attachment_size' => 'integer',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    public function attachmentKind(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        $mime = strtolower((string) $this->attachment_mime);
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }

        return 'file';
    }

    public function attachmentUrl(): ?string
    {
        if (!$this->hasAttachment()) {
            return null;
        }

        return route('dashboard.messages.attachment', $this->id);
    }

    public function attachmentSizeLabel(): string
    {
        $bytes = (int) $this->attachment_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }

        return $bytes . ' B';
    }
}
