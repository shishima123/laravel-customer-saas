<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'パスワードがリセットされました。',
    'sent' => 'パスワードリセット用のメールを送信しました。',
    'throttled' => 'しばらくしてからもう一度実行してください。',
    'token' => 'このパスワードリセットトークンは無効です。',
    'user' => "入力されたメールアドレスではユーザーを見つけることができませんでした。",
    'disable' => "アカウントが無効です。",
    'email_reset_pass' => [
        'subject' => 'パスワードリセットのお知らせ',
        'reset_pass' => "パスワードを再設定する",
        'line_1' => "パスワードリセットのリクエストを受け付けました。",
        'line_2' => "このパスワードは:count分後に期限切れとなります。",
        'line_3' => "パスワードのリセットにお心当たりがない場合は、このメールを無視してください。"
    ]
];
