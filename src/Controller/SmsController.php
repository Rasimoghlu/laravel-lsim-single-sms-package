<?php

declare(strict_types=1);

namespace Sarkhanrasimoghlu\Lsim\Controller;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Sarkhanrasimoghlu\Lsim\Contracts\SmsServiceInterface;

/**
 * SMS Controller
 */
class SmsController extends Controller
{
    public function __construct(
        private readonly SmsServiceInterface $smsService
    ) {}

    /**
     * Show balance page
     */
    public function showBalance(): View
    {
        $response = $this->smsService->getBalance();
        
        return view('sms::balance', [
            'balance' => $response->isSuccessful() ? $response->getBalance() : 0,
            'success' => $response->isSuccessful()
        ]);
    }
}