<?php

namespace App\Enums;

enum LeadOrigin: string
{
    case LANDING = 'landing';
    case LP_BLACK_FRIDAY = 'lp_black_friday';
    case LP_PRODUTO_X = 'lp_produto_x';
    case GOOGLE_ADS = 'google_ads';
    case META_ADS = 'meta_ads';
    case TIKTOK_ADS = 'tiktok_ads';
    case YOUTUBE_ADS = 'youtube_ads';
    case ORGANIC_SEARCH = 'organic_search';
    case INSTAGRAM_BIO = 'instagram_bio';
    case INFLUENCER_MARIA = 'influencer_maria';
    case EMAIL_CAMPAIGN_JAN2025 = 'email_campaign_jan2025';
    case REFERRAL_PARTNER_X = 'referral_partner_x';
    case WHATSAPP_WIDGET = 'whatsapp_widget';
    case DIRECT = 'direct';
    case QR_CODE_EVENTO = 'qr_code_evento';
    case WEBINAR_REGISTRO = 'webinar_registro';

    public function label(): string
    {
        return match ($this) {
            self::LANDING => 'Landing Page',
            self::LP_BLACK_FRIDAY => 'Landing Black Friday',
            self::LP_PRODUTO_X => 'Landing Produto X',
            self::GOOGLE_ADS => 'Google Ads',
            self::META_ADS => 'Meta Ads (Facebook/Instagram)',
            self::TIKTOK_ADS => 'TikTok Ads',
            self::YOUTUBE_ADS => 'YouTube Ads',
            self::ORGANIC_SEARCH => 'Pesquisa Orgânica',
            self::INSTAGRAM_BIO => 'Instagram - Link da Bio',
            self::INFLUENCER_MARIA => 'Influenciadora Maria',
            self::EMAIL_CAMPAIGN_JAN2025 => 'E-mail Marketing Jan/2025',
            self::REFERRAL_PARTNER_X => 'Parceiro X',
            self::WHATSAPP_WIDGET => 'Widget do WhatsApp',
            self::DIRECT => 'Acesso Direto',
            self::QR_CODE_EVENTO => 'QR Code Evento',
            self::WEBINAR_REGISTRO => 'Registro após Webinar',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
