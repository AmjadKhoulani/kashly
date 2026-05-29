<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\LedgerEntry;
use App\Models\LedgerPayment;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        try {
            $entries = LedgerEntry::all();
            foreach ($entries as $entry) {
                if (empty($entry->notes)) {
                    continue;
                }

                $lines = explode("\n", $entry->notes);
                $remainingLines = [];
                $hasChanged = false;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    // Check if the line matches the charge pattern:
                    // "29/05/2026: أضيف 100.00 USD — بالسيارة"
                    // "29/05/2026: أضيف 71.94 USD (ببسعر 13900 1000000 SYP) — لتصليح الكمبيوتر"
                    if (preg_match('/^(\d{2}\/\d{2}\/\d{4}):\s*أضيف\s*([0-9,.]+)\s*([A-Za-z]{3})(.*)$/u', $line, $matches)) {
                        $dateStr = $matches[1];
                        $amount = (float) str_replace(',', '', $matches[2]);
                        $currency = $matches[3];
                        $rest = trim($matches[4]); // Contains "(...) — Notes" or "— Notes"

                        try {
                            $paymentDate = Carbon::createFromFormat('d/m/Y', $dateStr)->format('Y-m-d');
                        } catch (\Exception $e) {
                            $paymentDate = Carbon::now()->format('Y-m-d');
                        }

                        $originalAmount = null;
                        $originalCurrency = null;
                        $exchangeRate = null;

                        if (preg_match('/\((?:ب*بسعر\s*([0-9.]+)\s*([0-9.]+)\s*([A-Za-z]{3})|([0-9.]+)\s*([A-Za-z]{3})\s*ب*بسعر\s*([0-9.]+))\)/u', $rest, $altMatches)) {
                            if (!empty($altMatches[1])) {
                                $exchangeRate = (float) $altMatches[1];
                                $originalAmount = (float) $altMatches[2];
                                $originalCurrency = $altMatches[3];
                            } else {
                                $originalAmount = (float) $altMatches[4];
                                $originalCurrency = $altMatches[5];
                                $exchangeRate = (float) $altMatches[6];
                            }
                        }

                        $notes = null;
                        if (preg_match('/—\s*(.*)$/u', $rest, $noteMatches)) {
                            $notes = trim($noteMatches[1]);
                        }

                        LedgerPayment::create([
                            'ledger_entry_id'   => $entry->id,
                            'type'              => 'charge',
                            'user_id'           => $entry->user_id,
                            'amount'            => $amount,
                            'currency'          => $currency,
                            'original_amount'   => $originalAmount,
                            'original_currency' => $originalCurrency,
                            'exchange_rate'     => $exchangeRate,
                            'payment_date'      => $paymentDate,
                            'notes'             => $notes,
                        ]);

                        $hasChanged = true;
                    } else {
                        $remainingLines[] = $line;
                    }
                }

                if ($hasChanged) {
                    $entry->notes = count($remainingLines) > 0 ? implode("\n", $remainingLines) : null;
                    $entry->save();
                }
            }
        } catch (\Exception $e) {
            logger()->error('Ledger text charges migration failed: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // No rollback needed for data conversion
    }
};
