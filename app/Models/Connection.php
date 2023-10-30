<?php

namespace App\Models;

use App\Tools;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    const TYPE_FTP = 'ftp';
    const TYPE_SFTP = 'sftp';
    const TYPE_MAUTIC = 'mautic';
    const TYPE_WEBHOOK = 'webhook';

    protected $guarded = ['id'];

    public static function types(): array
    {
        return [
            self::TYPE_FTP,
            self::TYPE_SFTP,
            self::TYPE_MAUTIC,
            self::TYPE_WEBHOOK
        ];
    }

    public static function typesByTool(string $tool): array
    {
        switch ($tool) {
            case Tools::REFERER:
            case Tools::DRIP_FEED:
                return [
                    self::TYPE_MAUTIC,
                    self::TYPE_WEBHOOK,
                ];
            default:
                return [];
        }
    }

    public function requiresUsername(): bool
    {
        return in_array($this->type, [Connection::TYPE_MAUTIC, Connection::TYPE_MAUTIC]);
    }

    public function requiresPassword(): bool
    {
        return in_array($this->type, [Connection::TYPE_MAUTIC, Connection::TYPE_MAUTIC]);
    }

    public function requiresHost(): bool
    {
        return $this->tool === Tools::DRIP_FEED;
    }

    public function requiresBaseUrl(): bool
    {
        return true;
    }

    public function requiresRootPath(): bool
    {
        return in_array($this->type, [Connection::TYPE_FTP, Connection::TYPE_SFTP]);
    }

    public function requiresWebhookUrl(): bool
    {
        return $this->tool === Tools::DRIP_FEED;
    }

    public function requiresCustomCode(): bool
    {
        return $this->tool === Tools::DRIP_FEED;
    }

    public function requiresLinkCustomCode(): bool
    {
        return $this->tool === Tools::DRIP_FEED;
    }

    public function scopeByTool(Builder $query, string $tool): Builder
    {
        return $query->where('tool', '=', $tool);
    }
}
