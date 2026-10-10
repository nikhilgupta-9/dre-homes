<?php
// Settings for the AI features. No keys here: they are read from the secrets file.
return [
    // --- Embeddings ---
    // 'gemini' (key: gemini_api_key) or 'voyage' (key: voyage_api_key).
    // Changing the provider, model or size makes reindex.php embed every chunk again.
    'embedding_provider' => 'gemini',
    'embedding_dim' => 768,
    'embedding_batch_size' => 50,
    // Texts sent per minute at most. The Gemini free tier allows 100 a minute; with billing
    // switched on in Google AI Studio this can be raised a lot (0 = no pause).
    'embedding_texts_per_minute' => 90,
    'embedding_providers' => [
        'gemini' => [
            'model' => 'gemini-embedding-001',
            'api_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
            'secret' => 'gemini_api_key',
        ],
        'voyage' => [
            'model' => 'voyage-4',
            'api_url' => 'https://api.voyageai.com/v1/embeddings',
            'secret' => 'voyage_api_key',
        ],
    ],

    // --- Chatbot (Google Gemini) ---
    // The chatbot is switched on with 'ai_chat_enabled' => true in the secrets file.
    // Models are tried in this order; the next one is used when one is overloaded or rate limited.
    // Measured on 2026-10-06 with the same question: 3.5-flash answered in about 3 seconds and was not
    // overloaded; 3.5-flash-lite was as fast but picked filters less carefully; 3.8-flash was often overloaded.
    'chat_models' => ['gemini-3.5-flash', 'gemini-3.5-flash-lite', 'gemini-3.8-flash'],
    // How much the model reasons before answering: 'low' keeps answers fast. '' = provider default.
    'chat_thinking_level' => 'low',
    // Seconds to wait for one model before trying the next.
    'chat_timeout_seconds' => 20,
    'chat_api_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    'chat_secret' => 'gemini_api_key',
    'chat_max_output_tokens' => 1500,
    'chat_temperature' => 0.3,
    'chat_max_tool_rounds' => 4,

    // Limits on the public endpoint (rule.md section 4, rule 7).
    'chat_max_message_chars' => 1000,
    'chat_max_turns' => 30,            // visitor messages per conversation
    'chat_history_turns' => 8,         // earlier visitor messages sent back to the model
    'chat_per_ip_per_minute' => 8,
    'chat_per_ip_per_hour' => 60,
    'chat_per_conversation_per_minute' => 6,
    'chat_max_leads_per_conversation' => 1,
    'chat_max_leads_per_ip_per_day' => 5,

    // Daily ceiling for all chat traffic together. When either is reached the chatbot
    // answers with the "contact us" message and stops calling the provider until midnight.
    'chat_daily_token_cap' => 3000000,
    'chat_daily_cost_cap_usd' => 5.00,
    // US dollars per one million tokens, used for the cost estimate in tbl_ai_usage.
    // CHECK these against the current Google price list before relying on the cost cap.
    'chat_price_per_million' => ['input' => 0.30, 'output' => 2.50],

    // Chat transcripts are deleted after this many days (ai/cron/cleanup.php).
    'chat_retention_days' => 90,

    // --- Chunking ---
    // Sizes are in characters; about four characters make one token.
    'chunk_max_chars' => 1600,
    'chunk_min_chars' => 200,

    // --- What may be read for the index ---
    // This is an allowlist of tables AND columns. Anything not written here cannot be
    // read by the indexer (Indexer::readAllowed refuses it), so contact details, internal
    // ids, and every lead, enquiry, resume, login and log table stay out.
    'index_allowlist' => [
        'tbl_proj' => [
            'prj_id', 'prj_type_id', 'prj_dev_id', 'prj_comm_id', 'proj_name', 'proj_sub_heading', 'proj_location',
            'proj_inventory_type', 'proj_pplan_cont', 'proj_overview', 'proj_fea_fac_cont', 'proj_fea_fac_opt',
            'proj_loc_cont', 'proj_masterplan', 'proj_slug', 'is_active',
            'proj_show_handover', 'proj_show_bedroom', 'proj_show_payment_plan', 'proj_show_size',
        ],
        'tbl_ai_proj_facts' => [
            'prj_id', 'price_aed', 'price_period', 'price_on_request', 'bedrooms_list', 'size_sqft',
            'handover_year', 'handover_quarter', 'handover_completed',
        ],
        'tbl_proj_type' => ['prj_type_id', 'proj_type_name', 'is_active'],
        'tbl_proj_faq' => ['proj_faq_id', 'proj_id', 'faq_heading', 'faq_descp', 'is_active'],
        'tbl_proj_payplan' => ['proj_payplan_id', 'proj_id', 'payplan_heading', 'payplan_percentage', 'payplan_status', 'is_active'],
        'tbl_proj_featfac' => ['featfac_id', 'featfac_name', 'is_active'],
        'tbl_community' => ['comm_id', 'comm_heading', 'comm_location', 'comm_long_descp', 'comm_loc_main_heading', 'comm_loc_main_descp', 'comm_slug', 'is_active'],
        'tbl_community_loc' => ['comm_loc_id', 'comm_id', 'comm_loc_heading', 'comm_loc_description', 'is_active'],
        'tbl_developer' => ['dev_id', 'dev_heading', 'dev_short_descp', 'dev_long_descp', 'choose_us_desc', 'dev_slug', 'is_active'],
        'tbl_blog' => ['blog_id', 'blog_heading', 'blog_short_descp', 'blog_long_descp', 'blog_slug', 'blog_posted_on', 'is_active'],
        'tbl_news' => ['news_id', 'news_heading', 'news_short_descp', 'news_long_descp', 'news_slug', 'news_posted_on', 'is_active'],
    ],

    // Public URL of a project page, by project type. Types not listed here have no page and are not indexed.
    'project_url_prefix' => [
        1 => 'property/',
        2 => 'ready-to-move-in/',
        3 => 'international-properties/',
        7 => 'buy-property/',
        8 => 'rent-property/',
    ],

    // Static public pages whose text is written in the PHP file itself. The indexer requests
    // each one from our own site and keeps the text between the content markers.
    'static_pages' => [
        'buyer-guide.php' => 'Buyer Guide',
        'seller-guide.php' => 'Seller Guide',
        'tenant-guide.php' => 'Tenant Guide',
        'landlord-guide.php' => 'Landlord Guide',
        'golden-visa.php' => 'Golden Visa',
        'services/mortgage-advisory' => 'Mortgage Advisory',
        'services/relocation-service' => 'Relocation Service',
        'services/property-and-investment-consultancy' => 'Property and Investment Consultancy',
        'services/bank-account-opening' => 'Bank Account Opening',
        'services/business-licenses' => 'Business Licenses',
        'about-us' => 'About DRE Homes',
    ],
];
