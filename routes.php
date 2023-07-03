<?php

Route::get('wechat/redirect','Wpjscc\\Weixin\Http\Controllers\WechatController@redirect')->name('wechat.redirect');
Route::get('wechat/callback','Wpjscc\\Weixin\Http\Controllers\WechatController@callback')->name('wechat.callback');