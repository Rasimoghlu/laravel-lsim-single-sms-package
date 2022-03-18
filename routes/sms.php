<?php

use Sarkhanrasimoghlu\Lsim\Controller\SmsController;
use Illuminate\Support\Facades\Route;

Route::get('/sms-balance', [SmsController::class, 'showBalance']);
