<?php
// Additional styles for image generation functionality
?>
<style>
/* Tab Navigation */
.tab-navigation {
    display: flex;
    background: var(--chat-header);
    border-radius: 15px 15px 0 0;
    overflow: hidden;
}

.tab-button {
    flex: 1;
    text-align: center;
    padding: 15px;
    background: var(--chat-primary);
    color: white;
    font-weight: bold;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-button.active {
    background: var(--chat-header);
    color: white;
    border-bottom: 3px solid var(--chat-secondary);
}

.tab-button i {
    margin-right: 8px;
}

/* Image generation specific styles */
.image-models-list-container {
    display: none;
    background-color: white;
    border-radius: 0 0 15px 15px;
    overflow: hidden;
    height: calc(100vh - 140px);
    max-height: 80vh;
    flex-direction: column;
}

.image-model-item {
    display: flex;
    padding: 12px 15px;
    border-bottom: 1px solid #F0F0F0;
    cursor: pointer;
    transition: background-color 0.2s;
}

.image-model-item:hover {
    background-color: #F5F5F5;
}

.image-model-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: var(--chat-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-right: 15px;
}

.image-generator-container {
    display: none;
    flex-direction: column;
    background-color: white;
    border-radius: 0 0 15px 15px;
    height: calc(100vh - 140px);
    max-height: 80vh;
}

.image-generator-header {
    display: flex;
    align-items: center;
    padding: 10px 16px;
    background-color: var(--chat-header);
    color: white;
}

.image-preview-area {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    background-color: var(--chat-bg);
    background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAIAAAAC64paAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAO0lEQVQ4y2P8//8/A7UBEwMNwKhBWg0aNYgIg9iIU4tIs8Zok8aAGsQzDcTH5DQ2iI0AYz7QFhJh0AAACBAreUQggYUAAAAASUVORK5CYII=');
    background-repeat: repeat;
    background-color: rgba(248, 248, 248, 0.95);
}

.image-result {
    max-width: 100%;
    margin: 10px 0;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.image-prompt-bubble {
    align-self: flex-end;
    background-color: var(--chat-sent);
    color: #303030;
    padding: 8px 12px;
    border-radius: 8px 8px 0 8px;
    margin: 5px 10px;
    max-width: 80%;
    word-break: break-word;
    position: relative;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.image-prompt-bubble::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: -8px;
    width: 8px;
    height: 13px;
    background-color: var(--chat-sent);
    border-bottom-left-radius: 10px;
}

.image-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 20px;
}

.image-generator-footer {
    padding: 10px;
    background-color: #F0F0F0;
    border-top: 1px solid #E0E0E0;
}

.image-options-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
}

.image-option {
    flex: 1;
    min-width: 100px;
}

.image-option label {
    color: #606060;
    font-size: 0.8rem;
    margin-bottom: 3px;
}

/* Image input field */
#imagePrompt {
    border-radius: 20px;
    resize: none;
    transition: all 0.3s ease;
    border: 1px solid #DDD;
    padding: 9px 12px;
}

#imagePrompt:focus {
    box-shadow: none;
    border-color: var(--chat-secondary);
}

/* Send button */
#sendImageBtn {
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

#sendImageBtn:hover {
    background-color: var(--chat-secondary);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .image-options-row {
        flex-direction: column;
    }
}
</style>
