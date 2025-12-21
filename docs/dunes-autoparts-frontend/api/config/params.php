<?php
return [
    // API Settings
    'api.version' => '1.0.0',
    'api.rateLimit' => 100, // requests per minute
    
    // LINE Messaging API
    'line.channelAccessToken' => '',
    'line.channelSecret' => '',
    
    // Facebook Messenger API
    'facebook.pageAccessToken' => '',
    'facebook.verifyToken' => '',
    'facebook.appSecret' => '',
    
    // AI/Chatbot Settings
    'openai.apiKey' => '',
    'openai.model' => 'gpt-3.5-turbo',
    'openai.maxTokens' => 500,
    
    // Webhook URLs
    'webhook.lineCallback' => '/api/chatbot/webhook/line',
    'webhook.facebookCallback' => '/api/chatbot/webhook/facebook',
];
