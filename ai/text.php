<?php require_once("backend/textChatBackend.php"); ?>
<?php require_once("backend/imageGenBackend.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Q8FLiX AI Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'css/textChatStyles.php'; ?>
    <?php include 'css/imageGenStyles.php'; ?>
</head>
<body>
    <div class="container mt-3 mt-md-5 px-0">
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <div class="tab-button active" id="chatTabButton">
                <i class="fas fa-comments"></i> Chat
            </div>
            <div class="tab-button" id="imageTabButton">
                <i class="fas fa-image"></i> Images
            </div>
        </div>

        <!-- Models List View (WhatsApp contacts style) -->
        <div class="models-list-container" id="modelsList">
            <div class="models-header">
                <h2 class="m-0"><i class="fas fa-comments me-2"></i>Q8FLiX AI Chat</h2>
            </div>
            <div class="models-search">
                <input type="text" id="modelSearch" placeholder="Search models..." class="form-control">
            </div>
            <div class="models-list" id="modelsListItems">
                <?php 
                if (is_array($models) && isset($models[0]) && is_array($models[0])) {
                    // API returned objects
                    foreach ($models as $modelObj): 
                        $name = $modelObj['name'] ?? 'unknown';
                        $desc = $modelObj['description'] ?? $name;
                        $firstLetter = strtoupper(substr($name, 0, 1));
                ?>
                <div class="model-item" data-model="<?php echo htmlspecialchars($name); ?>" data-desc="<?php echo htmlspecialchars($desc); ?>">
                    <div class="model-avatar"><?php echo htmlspecialchars($firstLetter); ?></div>
                    <div class="model-info">
                        <div class="model-name"><?php echo htmlspecialchars($desc); ?></div>
                        <div class="model-description">Start conversation...</div>
                    </div>
                </div>
                <?php 
                    endforeach;
                } elseif (is_array($models)) {
                    // Fallback or simple array of strings
                    foreach ($models as $model): 
                        $firstLetter = strtoupper(substr($model, 0, 1));
                ?>
                <div class="model-item" data-model="<?php echo htmlspecialchars($model); ?>" data-desc="<?php echo htmlspecialchars($model); ?>">
                    <div class="model-avatar"><?php echo htmlspecialchars($firstLetter); ?></div>
                    <div class="model-info">
                        <div class="model-name"><?php echo htmlspecialchars($model); ?></div>
                        <div class="model-description">Start conversation...</div>
                    </div>
                </div>
                <?php 
                    endforeach;
                }
                ?>
            </div>
        </div>

        <!-- Image Models List View -->
        <div class="image-models-list-container" id="imageModelsList">
            <div class="models-header">
                <h2 class="m-0"><i class="fas fa-image me-2"></i>Q8FLiX AI Image</h2>
            </div>
            <div class="models-search">
                <input type="text" id="imageModelSearch" placeholder="Search image models..." class="form-control">
            </div>
            <div class="models-list" id="imageModelsListItems">
                <?php 
                if (is_array($imageModels) && !empty($imageModels)) {
                    foreach ($imageModels as $modelObj): 
                        $name = $modelObj['name'] ?? 'unknown';
                        $desc = $modelObj['description'] ?? $name;
                        $firstLetter = strtoupper(substr($desc, 0, 1));
                ?>
                <div class="image-model-item" data-model="<?php echo htmlspecialchars($name); ?>" data-desc="<?php echo htmlspecialchars($desc); ?>">
                    <div class="image-model-avatar"><?php echo htmlspecialchars($firstLetter); ?></div>
                    <div class="model-info">
                        <div class="model-name"><?php echo htmlspecialchars($desc); ?></div>
                        <div class="model-description"><?php echo htmlspecialchars($modelObj['type'] ?? 'Image generation'); ?></div>
                    </div>
                </div>
                <?php 
                    endforeach;
                }
                ?>
            </div>
        </div>

        <!-- Chat View -->
        <div class="chat-container" id="chatContainer" style="display: none;">
            <div class="chat-header">
                <div class="back-button me-2" id="backButton">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="avatar" id="modelAvatar">A</div>
                <div class="chat-info">
                    <h2 id="modelName">AI Model</h2>
                    <small>Powered by Q8FLiX</small>
                </div>
                <div class="clear-chat-button" id="clearChatButton" style="color: white; cursor: pointer; opacity: 0.8;" title="Clear chat history">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
            <div class="chat-messages" id="chat">
                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-indicator-container">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <form id="chatForm">
                    <input type="hidden" id="model" name="model" value="">
                    <div class="input-group">
                        <textarea id="prompt" name="prompt" class="form-control message-input" placeholder="Type a message..." rows="1" required></textarea>
                        <button type="submit" class="btn send-button" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image Generator View -->
        <div class="image-generator-container" id="imageGeneratorContainer" style="display: none;">
            <div class="image-generator-header">
                <div class="back-button me-2" id="imageBackButton">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <div class="avatar" id="imageModelAvatar">A</div>
                <div class="chat-info">
                    <h2 id="imageModelName">AI Image Model</h2>
                    <small>Powered by Q8FLiX</small>
                </div>
                <div class="clear-chat-button" id="clearImageHistoryButton" style="color: white; cursor: pointer; opacity: 0.8;" title="Clear image history">
                    <i class="fas fa-trash-alt"></i>
                </div>
            </div>
            <div class="image-preview-area" id="imagePreviewArea">
                <!-- Image results will be displayed here -->
            </div>
            <div class="image-generator-footer">
                <form id="imageGenForm">
                    <input type="hidden" id="imageModel" name="model" value="">
                    <div class="image-options-row">
                        <div class="image-option">
                            <label for="imageWidth">Width</label>
                            <input type="number" id="imageWidth" name="width" class="form-control form-control-sm" value="512" min="256" max="1024">
                        </div>
                        <div class="image-option">
                            <label for="imageHeight">Height</label>
                            <input type="number" id="imageHeight" name="height" class="form-control form-control-sm" value="512" min="256" max="1024">
                        </div>
                        <div class="image-option">
                            <label for="imageSteps">Steps</label>
                            <input type="number" id="imageSteps" name="steps" class="form-control form-control-sm" value="30" min="10" max="100">
                        </div>
                    </div>
                    <div class="input-group">
                        <textarea id="imagePrompt" name="prompt" class="form-control message-input" placeholder="Describe the image you want to create..." rows="1" required></textarea>
                        <button type="submit" class="btn send-button" id="sendImageBtn">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'js/textChatScript.php'; ?>
    <?php include 'js/imageGenScript.php'; ?>
</body>
</html>