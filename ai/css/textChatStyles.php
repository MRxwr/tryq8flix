<style>
    :root {
        --chat-primary: #128C7E;
        --chat-secondary: #25D366;
        --chat-light: #DCF8C6;
        --chat-bg: #F5F5F5;  /* Lighter background color */
        --app-height: 100%;
        --chat-header: #075E54;
        --chat-sent: #DCF8C6;
        --chat-received: #FFFFFF;
    }
    html, body {
        height: var(--app-height);
        overflow: hidden;
    }
    body {
        background-color: var(--chat-bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .chat-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%; /* Use full container height */
    }
    .chat-header {
        background: var(--chat-header);
        color: white;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .chat-header .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--chat-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        font-weight: bold;
    }
    .chat-header .chat-info {
        flex-grow: 1;
    }
    .chat-header h2 {
        font-size: 16px;
        margin: 0;
        padding: 0;
    }
    .chat-messages {
        flex-grow: 1; /* Allow this to grow */
        overflow-y: auto;
        padding: 16px;
        background-color: var(--chat-bg);
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAIAAAAC64paAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAO0lEQVQ4y2P8//8/A7UBEwMNwKhBWg0aNYgIg9iIU4tIs8Zok8aAGsQzDcTH5DQ2iI0AYz7QFhJh0AAACBAreUQggYUAAAAASUVORK5CYII=');
        background-repeat: repeat;
        background-color: rgba(248, 248, 248, 0.95);  /* Lighter color with higher opacity */
    }
    .user-message {
        background-color: var(--chat-sent);
        color: #303030;
        border-radius: 8px 8px 0 8px;
        padding: 8px 12px;
        max-width: 80%;
        margin-left: auto;
        margin-bottom: 12px;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    }
    .user-message::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: -8px;
        width: 8px;
        height: 13px;
        background-color: var(--chat-sent);
        border-bottom-left-radius: 10px;
    }
    .ai-message {
        background-color: var(--chat-received);
        color: #303030;
        border-radius: 8px 8px 8px 0;
        padding: 8px 12px;
        max-width: 80%;
        margin-right: auto;
        margin-bottom: 12px;
        position: relative;
        box-shadow: 0 1px 0.5px rgba(0,0,0,.13);
    }
    .ai-message::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -8px;
        width: 8px;
        height: 13px;
        background-color: var(--chat-received);
        border-bottom-right-radius: 10px;
    }
    .error-message {
        background-color: #FFCCCC;
        color: #CC0000;
        border-radius: 8px;
        padding: 8px 12px;
        max-width: 90%;
        margin: 0 auto 12px auto;
        text-align: center;
    }
    .message-time {
        font-size: 0.65rem;
        margin-top: 4px;
        opacity: 0.7;
        text-align: right;
    }
    .chat-footer {
        padding: 10px;
        background-color: #F0F0F0;
        border-top: 1px solid #E0E0E0;
    }
    .message-input {
        border-radius: 20px;
        resize: none;
        transition: all 0.3s ease;
        border: 1px solid #DDD;
        padding: 9px 12px;
    }
    .message-input:focus {
        box-shadow: none;
        border-color: var(--chat-secondary);
    }
    .send-button {
        background-color: var(--chat-primary);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .send-button:hover {
        background-color: var(--chat-secondary);
    }
    .typing-indicator {
        display: none;
        align-items: center;
        margin-bottom: 12px;
    }
    .typing-indicator-container {
        background: white;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-block;
        box-shadow: 0 1px 0.5px rgba(0,0,0,.13);
    }
    .typing-indicator span {
        height: 8px;
        width: 8px;
        border-radius: 50%;
        background-color: var(--chat-primary);
        display: inline-block;
        margin-right: 5px;
        animation: typing 1s infinite ease-in-out;
    }
    .typing-indicator span:nth-child(1) {
        animation-delay: 0.1s;
    }
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.3s;
        margin-right: 0;
    }
    
    /* History truncation notice */
    .history-truncated-notice {
        width: 100%;
        margin-bottom: 12px;
    }
    .history-truncated-notice .alert {
        padding: 8px;
        border-radius: 8px;
        background-color: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.2);
        color: #0d6efd;
        font-size: 0.8rem;
    }
    
    /* Model List (Contacts) Styling */
    .models-list-container {
        background: white;
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    .models-header {
        background: var(--chat-header);
        color: white;
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }
    .models-search {
        margin-top: 10px;
        padding: 8px 15px;
        background: white;
        border-bottom: 1px solid #E0E0E0;
    }
    .models-search input {
        width: 100%;
        padding: 8px 12px;
        border-radius: 20px;
        border: 1px solid #DDD;
        background-color: #F0F0F0;
    }
    .models-list {
        overflow-y: auto;
        flex-grow: 1;
    }
    .model-item {
        padding: 12px 15px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #F0F0F0;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .model-item:hover {
        background-color: #F5F5F5;
    }
    .model-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--chat-primary);
        margin-right: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 20px;
        flex-shrink: 0; /* Prevent the avatar from shrinking */
    }
    .model-info {
        flex-grow: 1;
        overflow: hidden; /* Add this to make text-overflow work in flex child */
    }
    .model-name {
        font-weight: bold;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .model-description {
        font-size: 0.8rem;
        color: #606060;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @keyframes typing {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    @media (min-width: 577px) {
        .container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .chat-container, .image-generator-container {
            height: 90vh;
            max-height: 800px;
        }
    }
    @media (max-width: 768px) {
        .user-message, .ai-message {
            max-width: 90%;
        }
    }
    @media (max-width: 576px) {
        html, body {
            height: 100%;
            width: 100%;
            padding: 0;
            margin: 0;
            overflow: hidden;
            background-color: white;
            position: fixed;
            top: 0;
            left: 0;
        }
        .container {
            max-width: 100%;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            position: absolute;
            top: 0;
            left: 0;
        }
        .chat-container {
            border-radius: 0;
            height: 100%;
            width: 100%;
            max-width: 100%;
            margin: 0;
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            box-shadow: none;
        }
        .chat-header {
            border-radius: 0;
            position: relative;
            z-index: 10;
        }
        .chat-messages {
            flex-grow: 1;
            height: auto;
        }
        .chat-footer {
            padding-bottom: env(safe-area-inset-bottom, 15px);
            position: relative;
            z-index: 10;
        }
        .mt-3, .mt-md-5 {
            margin-top: 0 !important;
        }
    }
</style>