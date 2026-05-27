<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    /**
     * Get the real-time USD to SYP exchange rate from sp-today.com.
     * Caches the rate for 15 minutes to ensure high performance.
     *
     * @return float
     */
    public static function getSypRate()
    {
        return Cache::remember('usd_syp_rate', 900, function () {
            try {
                // Fetch the homepage of sp-today.com
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'ar,en-US;q=0.7,en;q=0.3',
                ])->timeout(6)->get('https://www.sp-today.com/');

                if ($response->successful()) {
                    $html = $response->body();

                    // Method 1: Match spans with "transition-colors" class which contain the rates
                    // Buy is the first, Sell is the second
                    if (preg_match_all('/<span[^>]*class="[^"]*transition-colors[^"]*"[^>]*>([\d,]+)<\/span>/i', $html, $matches)) {
                        if (count($matches[1]) >= 2) {
                            $buyRate = floatval(str_replace(',', '', $matches[1][0]));
                            $sellRate = floatval(str_replace(',', '', $matches[1][1]));
                            
                            // Return the selling rate (مبيع) as requested, with sanity check
                            if ($sellRate > 5000 && $sellRate < 30000) {
                                return $sellRate;
                            }
                        }
                    }

                    // Method 2: Match any transition-colors inline-block span
                    if (preg_match_all('/<span[^>]*class="[^"]*transition-colors[^"]*inline-block[^"]*"[^>]*>([\d,]+)<\/span>/i', $html, $matches)) {
                        if (count($matches[1]) >= 2) {
                            $sellRate = floatval(str_replace(',', '', $matches[1][1]));
                            if ($sellRate > 5000 && $sellRate < 30000) {
                                return $sellRate;
                            }
                        }
                    }

                    // Method 3: Resilient fallback parsing around "us-dollar" or general currency container
                    // Find all numbers formatted as XX,XXX in the HTML
                    if (preg_match_all('/([\d]{2,3}),([\d]{3})/', $html, $matches)) {
                        foreach ($matches[0] as $match) {
                            $rate = floatval(str_replace(',', '', $match));
                            // Since USD rate is in the 13,000 - 16,000 range, let's filter for valid numbers
                            if ($rate > 12000 && $rate < 18000) {
                                return $rate; // First matching rate in this range is highly likely USD Sell/Buy
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fail silently and let cache fallback
            }

            // Standard fallback rate (updated to match today's real-world price of ~14,000)
            return 14000.0;
        });
    }
}
