<?php
// JavaScript for image generation functionality
?>
<script>
// Image Generation Variables
let currentImageModel = '';
let imageSettings = {
    width: 512,
    height: 512,
    steps: 30
};

// Tab Navigation
document.addEventListener('DOMContentLoaded', function() {
    // Function to check session status (for debugging)
    function checkImageSessionStatus() {
        fetch('?debug_image_session=1')
            .then(response => response.json())
            .then(data => {
                console.log("Image Session status:", data);
            })
            .catch(error => console.error("Error checking image session:", error));
    }
    
    // Check session on page load
    checkImageSessionStatus();
    
    // Tab switching functionality
    const chatTabButton = document.getElementById('chatTabButton');
    const imageTabButton = document.getElementById('imageTabButton');
    const chatTabButtonImg = document.getElementById('chatTabButtonImg');
    const imageTabButtonImg = document.getElementById('imageTabButtonImg');
    
    const modelsList = document.getElementById('modelsList');
    const imageModelsList = document.getElementById('imageModelsList');
    const chatContainer = document.getElementById('chatContainer');
    const imageGeneratorContainer = document.getElementById('imageGeneratorContainer');
    
    // Functions to switch between tabs
    function switchToChat() {
        // Update active tab indicators
        chatTabButton.classList.add('active');
        imageTabButton.classList.remove('active');
        chatTabButtonImg.classList.add('active');
        imageTabButtonImg.classList.remove('active');
        
        // Show chat interface, hide image interface
        if (chatContainer.style.display === 'flex') {
            modelsList.style.display = 'flex';
            chatContainer.style.display = 'none';
        } else {
            modelsList.style.display = 'flex';
        }
        
        imageModelsList.style.display = 'none';
        imageGeneratorContainer.style.display = 'none';
    }
    
    function switchToImage() {
        // Update active tab indicators
        imageTabButton.classList.add('active');
        chatTabButton.classList.remove('active');
        imageTabButtonImg.classList.add('active');
        chatTabButtonImg.classList.remove('active');
        
        // Show image interface, hide chat interface
        if (imageGeneratorContainer.style.display === 'flex') {
            imageModelsList.style.display = 'flex';
            imageGeneratorContainer.style.display = 'none';
        } else {
            imageModelsList.style.display = 'flex';
        }
        
        modelsList.style.display = 'none';
        chatContainer.style.display = 'none';
    }
    
    // Add event listeners for tab buttons
    chatTabButton.addEventListener('click', switchToChat);
    chatTabButtonImg.addEventListener('click', switchToChat);
    imageTabButton.addEventListener('click', switchToImage);
    imageTabButtonImg.addEventListener('click', switchToImage);
    
    // Image model selection
    const imageModelItems = document.querySelectorAll('.image-model-item');
    imageModelItems.forEach(model => {
        model.addEventListener('click', function() {
            const modelName = this.getAttribute('data-model');
            const modelDesc = this.getAttribute('data-desc');
            const firstLetter = modelDesc.charAt(0).toUpperCase();
            
            // Set up the image generator view
            document.getElementById('imageModelName').textContent = modelDesc;
            document.getElementById('imageModelAvatar').textContent = firstLetter;
            document.getElementById('imageModel').value = modelName;
            
            // Switch view
            imageModelsList.style.display = 'none';
            imageGeneratorContainer.style.display = 'flex';
            
            // Set current model and load history
            currentImageModel = modelName;
            loadImageHistory(currentImageModel);
        });
    });
    
    // Back button for image generator
    document.getElementById('imageBackButton').addEventListener('click', function() {
        imageGeneratorContainer.style.display = 'none';
        imageModelsList.style.display = 'flex';
    });
    
    // Clear image history button
    document.getElementById('clearImageHistoryButton').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the image history?')) {
            clearImageHistory(currentImageModel);
        }
    });
    
    // Handle image generation form submission
    document.getElementById('imageGenForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const model = document.getElementById('imageModel').value;
        const prompt = document.getElementById('imagePrompt').value;
        const width = document.getElementById('imageWidth').value;
        const height = document.getElementById('imageHeight').value;
        const steps = document.getElementById('imageSteps').value;
        
        // Display the prompt bubble
        const promptBubble = document.createElement('div');
        promptBubble.className = 'image-prompt-bubble';
        promptBubble.textContent = prompt;
        document.getElementById('imagePreviewArea').appendChild(promptBubble);
        
        // Display loading indicator
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'image-loading';
        loadingDiv.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Generating image...</p>
        `;
        document.getElementById('imagePreviewArea').appendChild(loadingDiv);
        document.getElementById('imagePreviewArea').scrollTop = document.getElementById('imagePreviewArea').scrollHeight;
        
        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'generate');
        formData.append('model', model);
        formData.append('prompt', prompt);
        formData.append('width', width);
        formData.append('height', height);
        formData.append('steps', steps);
        
        // Submit the request
        fetch('backend/imageGenBackend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading indicator
            loadingDiv.remove();
            
            if (data.success) {
                // Create image element
                const img = document.createElement('img');
                img.src = data.image_url;
                img.alt = prompt;
                img.className = 'image-result';
                
                // Add to preview area
                document.getElementById('imagePreviewArea').appendChild(img);
                
                // Scroll to the bottom
                document.getElementById('imagePreviewArea').scrollTop = document.getElementById('imagePreviewArea').scrollHeight;
                
                // Reset prompt field
                document.getElementById('imagePrompt').value = '';
            } else {
                // Display error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = 'Error generating image: ' + (data.message || 'Unknown error');
                document.getElementById('imagePreviewArea').appendChild(errorDiv);
            }
        })
        .catch(error => {
            // Remove loading indicator
            loadingDiv.remove();
            
            // Display error
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.textContent = 'Error: ' + error.message;
            document.getElementById('imagePreviewArea').appendChild(errorDiv);
        });
    });
    
    // Image search functionality
    document.getElementById('imageModelSearch').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const models = document.querySelectorAll('.image-model-item');
        
        models.forEach(model => {
            const name = model.getAttribute('data-desc').toLowerCase();
            if (name.includes(searchValue)) {
                model.style.display = 'flex';
            } else {
                model.style.display = 'none';
            }
        });
    });
    
    // Function to load image history for a model from the session
    function loadImageHistory(model) {
        console.log(`Loading image history for model: ${model}`);
        
        // Show loading indicator
        const previewArea = document.getElementById('imagePreviewArea');
        previewArea.innerHTML = `
            <div class="image-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading image history...</p>
            </div>
        `;
        
        // Fetch history from server
        const formData = new FormData();
        formData.append('action', 'get_history');
        formData.append('model', model);
        
        fetch('backend/imageGenBackend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Image history data received:", data);
            
            // Clear current preview area
            previewArea.innerHTML = '';
            
            if (data.status === 'success' && data.history && data.history.length > 0) {
                // Display each image in history
                data.history.forEach(item => {
                    const promptBubble = document.createElement('div');
                    promptBubble.className = 'image-prompt-bubble';
                    promptBubble.textContent = item.prompt;
                    previewArea.appendChild(promptBubble);
                    
                    const img = document.createElement('img');
                    img.src = item.imageUrl;
                    img.alt = item.prompt;
                    img.className = 'image-result';
                    previewArea.appendChild(img);
                });
            } else {
                console.log("No image history found for model:", model);
            }
            
            // Scroll to the bottom
            previewArea.scrollTop = previewArea.scrollHeight;
        })
        .catch(error => {
            console.error('Error loading image history:', error);
            previewArea.innerHTML = `
                <div class="alert alert-danger m-3">
                    Error loading image history. Please try again.
                </div>
            `;
        });
    }
    
    // Function to clear image history for a model
    function clearImageHistory(model) {
        const formData = new FormData();
        formData.append('action', 'clear_history');
        formData.append('model', model);
        
        fetch('backend/imageGenBackend.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Clear image history response:", data);
            
            if (data.status === 'success') {
                // Clear the UI
                document.getElementById('imagePreviewArea').innerHTML = '';
            } else {
                console.error("Failed to clear image history:", data.message);
            }
        })
        .catch(error => {
            console.error('Error clearing image history:', error);
        });
    }
    
    // Auto-resize text area
    const imagePromptTextarea = document.getElementById('imagePrompt');
    imagePromptTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Load saved image settings from localStorage if available
    function loadImageSettings() {
        const savedSettings = localStorage.getItem('imageSettings');
        if (savedSettings) {
            try {
                imageSettings = JSON.parse(savedSettings);
                document.getElementById('imageWidth').value = imageSettings.width;
                document.getElementById('imageHeight').value = imageSettings.height;
                document.getElementById('imageSteps').value = imageSettings.steps;
                
                // Update modal values too
                document.getElementById('modalImageWidth').value = imageSettings.width;
                document.getElementById('modalImageHeight').value = imageSettings.height;
                document.getElementById('modalImageSteps').value = imageSettings.steps;
                
                updateModalLabels();
            } catch (e) {
                console.error('Error loading saved image settings:', e);
            }
        }
    }
    
    // Save current settings to localStorage
    function saveImageSettings() {
        if (document.getElementById('saveImageSettings').checked) {
            localStorage.setItem('imageSettings', JSON.stringify(imageSettings));
        }
    }
    
    // Update settings when modal is opened
    document.getElementById('imageOptionsButton').addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('imageOptionsModal'));
        
        // Set current values to the modal inputs
        document.getElementById('modalImageWidth').value = imageSettings.width;
        document.getElementById('modalImageHeight').value = imageSettings.height;
        document.getElementById('modalImageSteps').value = imageSettings.steps;
        
        updateModalLabels();
        modal.show();
    });
    
    // Update the value labels when sliders change
    document.getElementById('modalImageWidth').addEventListener('input', updateModalLabels);
    document.getElementById('modalImageHeight').addEventListener('input', updateModalLabels);
    document.getElementById('modalImageSteps').addEventListener('input', updateModalLabels);
    
    function updateModalLabels() {
        document.getElementById('modalImageWidthValue').textContent = 
            document.getElementById('modalImageWidth').value + 'px';
        document.getElementById('modalImageHeightValue').textContent = 
            document.getElementById('modalImageHeight').value + 'px';
        document.getElementById('modalImageStepsValue').textContent = 
            document.getElementById('modalImageSteps').value;
    }
    
    // Save settings when Apply button is clicked
    document.getElementById('saveImageOptions').addEventListener('click', function() {
        // Get values from modal
        const width = parseInt(document.getElementById('modalImageWidth').value);
        const height = parseInt(document.getElementById('modalImageHeight').value);
        const steps = parseInt(document.getElementById('modalImageSteps').value);
        
        // Update settings object
        imageSettings = {
            width: width,
            height: height,
            steps: steps
        };
        
        // Update hidden form fields
        document.getElementById('imageWidth').value = width;
        document.getElementById('imageHeight').value = height;
        document.getElementById('imageSteps').value = steps;
        
        // Save to localStorage if option is checked
        saveImageSettings();
        
        // Close the modal
        bootstrap.Modal.getInstance(document.getElementById('imageOptionsModal')).hide();
    });
    
    // Load settings on page load
    loadImageSettings();
});
</script>
