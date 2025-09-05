<script>
    // Fix for mobile browsers viewport height issues
    const appHeight = () => {
        const doc = document.documentElement;
        doc.style.setProperty('--app-height', `${window.innerHeight}px`);
    }
    window.addEventListener('resize', appHeight);
    appHeight();

    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const chatMessages = document.getElementById('chat');
        const chatForm = document.getElementById('chatForm');
        const promptInput = document.getElementById('prompt');
        const sendBtn = document.getElementById('sendBtn');
        const typingIndicator = document.getElementById('typingIndicator');
        const modelsList = document.getElementById('modelsList');
        const chatContainer = document.getElementById('chatContainer');
        const backButton = document.getElementById('backButton');
        const modelItems = document.querySelectorAll('.model-item');
        const modelSearch = document.getElementById('modelSearch');
        const modelNameElement = document.getElementById('modelName');
        const modelAvatarElement = document.getElementById('modelAvatar');
        const modelInput = document.getElementById('model');
        const modelTypeInput = document.createElement('input');
        modelTypeInput.type = 'hidden';
        modelTypeInput.id = 'modelType';
        modelTypeInput.name = 'type';
        chatForm.prepend(modelTypeInput);

        // Show models list by default
        modelsList.style.display = 'flex';
        chatContainer.style.display = 'none';

        // Handle model selection
        // Store the currently selected model
        let currentModel = '';
        
        // Function to check session status (for debugging)
        function checkSessionStatus() {
            fetch('?debug_session=1')
                .then(response => response.json())
                .then(data => {
                    console.log("Session status:", data);
                })
                .catch(error => console.error("Error checking session:", error));
        }
        
        // Check session on page load
        checkSessionStatus();
        
        modelItems.forEach(item => {
            item.addEventListener('click', function() {
                const modelName = this.getAttribute('data-model');
                const modelDesc = this.getAttribute('data-desc');
                const modelType = this.getAttribute('data-type');
                const firstLetter = modelDesc.charAt(0).toUpperCase();
                
                console.log(`Switching to model: ${modelName}, type: ${modelType}`);
                
                // Update chat view with selected model
                modelNameElement.textContent = modelDesc;
                modelAvatarElement.textContent = firstLetter;
                modelInput.value = modelName;
                modelTypeInput.value = modelType;
                currentModel = modelName;
                
                // Switch views
                modelsList.style.display = 'none';
                chatContainer.style.display = 'flex';
                
                // Clear the chat UI
                clearChatMessages();
                
                // Load chat history for this model
                loadChatHistory(modelName);
                
                // Focus on input
                setTimeout(() => {
                    promptInput.focus();
                }, 100);
            });
        });

        // Handle back button
        backButton.addEventListener('click', function() {
            // Remember the current model and its chat before switching views
            const lastModel = currentModel;
            
            console.log(`Back button clicked, remembering model: ${lastModel}`);
            
            // Switch views
            chatContainer.style.display = 'none';
            modelsList.style.display = 'flex';
            
            // Check session to ensure data is preserved
            checkSessionStatus();
        });
        
        // Handle clear chat button
        const clearChatButton = document.getElementById('clearChatButton');
        clearChatButton.addEventListener('click', function() {
            const model = modelInput.value;
            if (!model) return;
            
            if (confirm('Are you sure you want to clear the chat history?')) {
                // Send request to clear history
                const formData = new FormData();
                formData.append('action', 'clear_history');
                formData.append('model', model);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin' // Include cookies
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Clear chat UI
                        clearChatMessages();
                        console.log('Chat history cleared');
                    }
                })
                .catch(error => console.error('Error clearing history:', error));
            }
        });
        
        // Function to clear all messages from the chat UI
        function clearChatMessages() {
            // Remove all child elements except the typing indicator
            const children = Array.from(chatMessages.children);
            children.forEach(child => {
                if (child !== typingIndicator) {
                    chatMessages.removeChild(child);
                }
            });
        }
        
        // Function to load chat history for a model
        function loadChatHistory(model) {
            console.log(`Loading chat history for model: ${model}`);
            
            // Show loading indicator
            typingIndicator.style.display = 'block';
            
            // Add cache-busting parameter to prevent browser caching
            const timestamp = new Date().getTime();
            fetch(`?action=get_history&model=${encodeURIComponent(model)}&_=${timestamp}`, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                credentials: 'same-origin' // Include cookies
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("History data received:", data);
                    
                    if (data.status === 'success') {
                        if (data.history && data.history.length > 0) {
                            // Show truncation notice if history was limited
                            if (data.truncated) {
                                const noticeDiv = document.createElement('div');
                                noticeDiv.className = 'history-truncated-notice';
                                noticeDiv.innerHTML = `
                                    <div class="alert alert-info text-center small mb-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Showing the most recent ${data.limitedCount} of ${data.originalCount} messages 
                                        (${Math.round(data.charactersUsed / 1000 * 10) / 10}K characters)
                                    </div>
                                `;
                                chatMessages.appendChild(noticeDiv);
                                console.log(`Chat history truncated: ${data.limitedCount}/${data.originalCount} messages`);
                            }
                            
                            // Display each message in the UI
                            data.history.forEach(msg => {
                                const modelType = document.getElementById('modelType').value;
                                if (msg.role === 'user') {
                                    addMessage('user', msg.content);
                                } else if (msg.role === 'assistant') {
                                    addMessage('ai', msg.content, model, modelType);
                                }
                            });
                        } else {
                            console.log("No history found for model:", model);
                        }
                    } else {
                        console.error("Error in history response:", data);
                    }
                })
                .catch(error => {
                    console.error('Error loading chat history:', error);
                })
                .finally(() => {
                    // Hide loading indicator
                    typingIndicator.style.display = 'none';
                    // Scroll to bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }

        // Filter models on search
        modelSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            modelItems.forEach(item => {
                const modelName = item.getAttribute('data-desc').toLowerCase();
                if (modelName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Auto-resize textarea as user types
        promptInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Handle Enter key to submit the form, Shift+Enter to add a new line
        promptInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        // Handle form submission
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userMessage = formData.get('prompt').trim();
            
            if (!userMessage) return;
            
            const model = formData.get('model');
            
            // Log for debugging
            console.log(`Sending message for model: ${model}`);
            
            // Check session status before sending
            checkSessionStatus();
            
            // Disable inputs and show sending state
            sendBtn.disabled = true;
            promptInput.disabled = true;
            
            // Add user message to chat
            addMessage('user', userMessage);
            
            // Reset and focus the input
            promptInput.value = '';
            promptInput.style.height = 'auto';
            
            // Show typing indicator
            typingIndicator.style.display = 'block';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            fetch(window.location.href, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin' // Include cookies
            })
            .then(response => response.json())
            .then data => {
                // Hide typing indicator
                typingIndicator.style.display = 'none';
                
                // Log the full response to console for debugging
                console.log('API Response:', data);
                
                if (data.status === 'success') {
                    // Format and display AI response
                    addMessage('ai', data.content, data.model, data.type);
                } else {
                    addMessage('error', data.message);
                }
            })
            .catch(error => {
                // Hide typing indicator
                typingIndicator.style.display = 'none';
                addMessage('error', 'Network error: ' + error.message);
            })
            .finally(() => {
                // Re-enable inputs
                sendBtn.disabled = false;
                promptInput.disabled = false;
                // promptInput.focus(); // Removed to prevent keyboard from popping up on mobile
                
                // Scrolling is now handled within the addMessage function
            });
        });

        // Function to add a message to the chat
        function addMessage(type, content, model = '', modelType = 'text') {
            const messageDiv = document.createElement('div');
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            if (type === 'user') {
                messageDiv.className = 'user-message';
                messageDiv.innerHTML = `
                    <div>${escapeHtml(content)}</div>
                    <div class="message-time">${time}</div>
                `;
            } else if (type === 'ai') {
                messageDiv.className = 'ai-message';
                let displayContent = '';

                if (modelType === 'image' && content.includes('generated_images')) {
                    // It's an image response
                    displayContent = `<img src="${content}" class="img-fluid rounded" alt="Generated Image">`;
                } else {
                    // It's a text response, format it
                    displayContent = formatResponse(content);
                }
                
                messageDiv.innerHTML = `
                    <div><strong>${model}</strong>: ${displayContent}</div>
                    <div class="message-time">${time}</div>
                `;
            } else if (type === 'error') {
                messageDiv.className = 'error-message';
                messageDiv.innerHTML = `
                    <div><i class="fas fa-exclamation-triangle me-2"></i>${escapeHtml(content)}</div>
                    <div class="message-time">${time}</div>
                `;
            }
            
            // Insert before typing indicator
            chatMessages.insertBefore(messageDiv, typingIndicator);
            
            // Animate message appearance
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateY(20px)';
            messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            
            setTimeout(() => {
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
                
                // Scroll based on message type
                if (type === 'user') {
                    // Scroll to the bottom to see the typing indicator
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } else {
                    // For AI or error messages, scroll to the top of the new message
                    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 10);
        }
        
        // Format code blocks and URLs in responses
        function formatResponse(text) {
            // Basic security: escape HTML
            text = escapeHtml(text);
            
            // Format code blocks (```code```)
            text = text.replace(/```([\s\S]*?)```/g, '<pre class="bg-dark text-white p-2 mt-2 mb-2 rounded"><code>$1</code></pre>');
            
            // Format inline code (`code`)
            text = text.replace(/`([^`]+)`/g, '<code class="bg-light px-1 rounded">$1</code>');
            
            // Convert URLs to links
            text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
            
            // Convert line breaks to <br>
            text = text.replace(/\n/g, '<br>');
            
            return text;
        }
        
        // Helper function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    });
</script>