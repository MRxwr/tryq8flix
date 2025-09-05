<?php
// Additional styles for image generation functionality
?>
<style>
/* Tab Navigation */
.tab-navigation {
    display: flex;
    background: #1a1a1a;
    border-radius: 15px 15px 0 0;
    overflow: hidden;
}

.tab-button {
    flex: 1;
    text-align: center;
    padding: 15px;
    background: #333;
    color: #ccc;
    font-weight: bold;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-button.active {
    background: #1a1a1a;
    color: white;
    border-bottom: 3px solid #007bff;
}

.tab-button i {
    margin-right: 8px;
}

/* Image generation specific styles */
.image-models-list-container {
    display: none;
    background-color: #1a1a1a;
    border-radius: 0 0 15px 15px;
    overflow: hidden;
    height: calc(100vh - 140px);
    max-height: 80vh;
    flex-direction: column;
}

.image-model-item {
    display: flex;
    padding: 15px;
    border-bottom: 1px solid #333;
    cursor: pointer;
    transition: background-color 0.3s;
}

.image-model-item:hover {
    background-color: #333;
}

.image-model-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #007bff;
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
    background-color: #1a1a1a;
    border-radius: 0 0 15px 15px;
    height: calc(100vh - 140px);
    max-height: 80vh;
}

.image-generator-header {
    display: flex;
    align-items: center;
    padding: 15px;
    background-color: #252525;
    color: white;
}

.image-preview-area {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.image-result {
    max-width: 100%;
    margin: 10px 0;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.image-prompt-bubble {
    align-self: flex-end;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 18px 18px 0 18px;
    margin: 5px 10px;
    max-width: 80%;
    word-break: break-word;
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
    padding: 15px;
    background-color: #252525;
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
    color: #ccc;
    font-size: 0.8rem;
    margin-bottom: 3px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .image-options-row {
        flex-direction: column;
    }
}
</style>
