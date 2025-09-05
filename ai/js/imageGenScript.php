<?php
// JavaScript for image generation functionality
?>
<script>
// Image Generation Variables
let currentImageModel = '';
let imageHistory = {};

// Tab Navigation
document.addEventListener('DOMContentLoaded', function() {
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
            
            // Initialize or load history
            currentImageModel = modelName;
            if (!imageHistory[currentImageModel]) {
                imageHistory[currentImageModel] = [];
            } else {
                // Load previous images
                displayImageHistory();
            }
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
            imageHistory[currentImageModel] = [];
            document.getElementById('imagePreviewArea').innerHTML = '';
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
                
                // Save to history
                imageHistory[currentImageModel].push({
                    prompt: prompt,
                    imageUrl: data.image_url
                });
                
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
    
    // Function to display image history
    function displayImageHistory() {
        const history = imageHistory[currentImageModel];
        const previewArea = document.getElementById('imagePreviewArea');
        
        // Clear current preview area
        previewArea.innerHTML = '';
        
        // Display each image in history
        history.forEach(item => {
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
        
        // Scroll to the bottom
        previewArea.scrollTop = previewArea.scrollHeight;
    }
    
    // Auto-resize text area
    const imagePromptTextarea = document.getElementById('imagePrompt');
    imagePromptTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
