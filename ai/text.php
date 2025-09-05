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
        <!-- Models List View (WhatsApp contacts style) -->
        <div class="models-list-container" id="modelsList">
            <div class="models-header">
                <h2 class="m-0"><i class="fas fa-comments me-2"></i>Q8FLiX AI Hub</h2>
            </div>
            <div class="models-search">
                <input type="text" id="modelSearch" placeholder="Search models..." class="form-control">
            </div>
            
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <div class="tab-button active" id="chatTabButton">
                    <i class="fas fa-comments"></i><span>Chat</span>
                </div>
                <div class="tab-button" id="imageTabButton">
                    <i class="fas fa-image"></i><span>Images</span>
                </div>
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
                <h2 class="m-0"><i class="fas fa-image me-2"></i>Q8FLiX AI Hub</h2>
            </div>
            <div class="models-search">
                <input type="text" id="imageModelSearch" placeholder="Search image models..." class="form-control">
            </div>
            
            <!-- Tab Navigation (duplicate for image view) -->
            <div class="tab-navigation">
                <div class="tab-button" id="chatTabButtonImg">
                    <i class="fas fa-comments"></i><span>Chat</span>
                </div>
                <div class="tab-button active" id="imageTabButtonImg">
                    <i class="fas fa-image"></i><span>Images</span>
                </div>
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
                <div class="image-header-actions">
                    <div class="image-options-button" id="imageOptionsButton" title="Image Generation Options">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="clear-chat-button" id="clearImageHistoryButton" title="Clear Image History">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </div>
            <div class="image-preview-area" id="imagePreviewArea">
                <!-- Image results will be displayed here -->
            </div>
            <div class="image-generator-footer">
                <form id="imageGenForm">
                    <input type="hidden" id="imageModel" name="model" value="">
                    <input type="hidden" id="imageWidth" name="width" value="512">
                    <input type="hidden" id="imageHeight" name="height" value="512">
                    <input type="hidden" id="imageSteps" name="steps" value="30">
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
    
    <!-- Image Options Modal -->
    <div class="modal fade" id="imageOptionsModal" tabindex="-1" aria-labelledby="imageOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageOptionsModalLabel">Image Generation Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modalImageWidth" class="form-label">Width</label>
                        <input type="range" class="form-range" id="modalImageWidth" min="256" max="1024" step="64" value="512">
                        <div class="d-flex justify-content-between">
                            <small>256px</small>
                            <span id="modalImageWidthValue">512px</span>
                            <small>1024px</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modalImageHeight" class="form-label">Height</label>
                        <input type="range" class="form-range" id="modalImageHeight" min="256" max="1024" step="64" value="512">
                        <div class="d-flex justify-content-between">
                            <small>256px</small>
                            <span id="modalImageHeightValue">512px</span>
                            <small>1024px</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modalImageSteps" class="form-label">Steps</label>
                        <input type="range" class="form-range" id="modalImageSteps" min="10" max="100" step="5" value="30">
                        <div class="d-flex justify-content-between">
                            <small>10</small>
                            <span id="modalImageStepsValue">30</span>
                            <small>100</small>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="saveImageSettings" checked>
                        <label class="form-check-label" for="saveImageSettings">Save settings for future sessions</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveImageOptions">Apply Settings</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'js/textChatScript.php'; ?>
    <?php include 'js/imageGenScript.php'; ?>
</body>
</html>