// aiGenerator.js

document.addEventListener('DOMContentLoaded', function() {
    // Set the first model button as active by default
    document.querySelector('.model-btn').classList.add('active');

    // Initialize tooltips
    if (typeof $(document).tooltip === 'function') {
        $(document).tooltip();
    }

    // Add hover effects to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            if (!this.disabled) {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.3)';
            }
        });

        button.addEventListener('mouseleave', function() {
            if (!this.disabled) {
                this.style.transform = '';
                this.style.boxShadow = '';
            }
        });
    });
});

const promptInput = document.getElementById("prompt");
const submitBtn = document.getElementById("submitBtn");
const downloadBtn = document.getElementById("downloadBtn");
const savePromptBtn = document.getElementById("savePromptBtn");
const shareBtn = document.getElementById("shareBtn");
const refreshBtn = document.getElementById("refreshBtn");
const viewHistoryBtn = document.getElementById("viewHistoryBtn");
const clearCookiesBtn = document.getElementById("clearCookiesBtn");
const frame = document.getElementById("frame");
const historyList = document.getElementById("historyList");
const toastContainer = document.getElementById("toastContainer");
const loading = document.getElementById("loading");

const maxPrompts = 5;
const promptCookieName = "savedPrompts";
let generationTimeoutId = null; // Variable to hold the timeout ID
let currentSeed = 0; // Variable to hold the current seed for refresh
let lastSuccessfulPrompt = ""; // Store the last prompt that generated successfully

const showToast = (message, type = 'success') => {
    // Create blocking overlay
    const overlay = document.createElement("div");
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 2000;
    `;

    // Create centered toast
    const toast = document.createElement("div");
    const bgColor = type === 'error' ? 'bg-danger' : 'bg-success';
    const iconClass = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
    toast.className = `toast align-items-center text-white ${bgColor} border-0 show`;
    toast.role = "alert";
    toast.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        min-width: 300px;
        z-index: 2001;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    `;
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body"><i class="${iconClass} mr-2"></i>${message}</div>
        </div>`;

    document.body.appendChild(overlay);
    document.body.appendChild(toast);
    
    // Fade in
    setTimeout(() => {
        toast.style.opacity = "1";
    }, 50);

    // Fade out and remove
    setTimeout(() => {
        toast.style.opacity = "0";
        overlay.style.opacity = "0";
        setTimeout(() => {
            toast.remove();
            overlay.remove();
        }, 500);
    }, 2500);
};

// Function to handle image generation (used by both submit and refresh)
function generateImage(isRefresh = false) {
    const prompt = promptInput.value.trim();
    const selectedModel = document.querySelector(".model-btn.active")?.getAttribute("data-model") || "normal";
    const width = document.getElementById("imgWidth").value || 1080;
    const height = document.getElementById("imgHeight").value || 1080;
    
    if (!prompt) {
        showToast("Please enter a prompt first!", 'error');
        return;
    }

    // Clear any previous timeout
    if (generationTimeoutId) {
        clearTimeout(generationTimeoutId);
    }

    loading.style.display = "flex"; // Show loading indicator
    // Disable buttons during generation
    submitBtn.disabled = true;
    downloadBtn.disabled = true;
    savePromptBtn.disabled = true;
    shareBtn.disabled = true;
    refreshBtn.disabled = true; // Always disable refresh during any generation

    if (isRefresh) {
        currentSeed++; // Increment seed only on refresh
    } else {
        currentSeed = 0; // Reset seed if it's a new prompt generation
    }

    const modelParam = selectedModel !== "normal" ? `&model=${selectedModel}` : "";
    const seedParam = currentSeed > 0 ? `&seed=${currentSeed}` : ""; // Add seed if > 0
    const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(prompt)}?width=${width}&height=${height}&nologo=true${modelParam}${seedParam}`;
    
    console.log("Generating image with URL:", imageUrl); // For debugging
    frame.src = imageUrl;

    // Set a timeout for 60 seconds (60000 milliseconds)
    generationTimeoutId = setTimeout(() => {
        if (loading.style.display === "flex") {
            loading.style.display = "none";
            frame.src = "";
            submitBtn.disabled = false; // Re-enable generate button
            refreshBtn.disabled = true; // Ensure refresh is disabled on timeout
            showToast("Image generation timed out. Please try again.", 'error');
            generationTimeoutId = null;
        }
    }, 60000);

    frame.onload = () => {
        if (generationTimeoutId) {
            clearTimeout(generationTimeoutId);
            generationTimeoutId = null;
        }
        loading.style.display = "none";
        // Enable buttons on successful load
        downloadBtn.disabled = false;
        savePromptBtn.disabled = false;
        shareBtn.disabled = false;
        // Only enable refresh after the *first* successful generation for this prompt
        refreshBtn.disabled = false; 
        submitBtn.disabled = false; 
        lastSuccessfulPrompt = prompt; // Store the successful prompt
        showToast(`Image ${isRefresh ? 'refreshed' : 'generated'} successfully!`);
    };

    frame.onerror = () => {
        if (generationTimeoutId) {
            clearTimeout(generationTimeoutId);
            generationTimeoutId = null;
        }
        loading.style.display = "none";
        submitBtn.disabled = false; // Re-enable generate button
        refreshBtn.disabled = true; // Ensure refresh is disabled on error
        showToast("Error generating image. Please try again.", 'error');
    };
}

submitBtn.addEventListener("click", function () {
    generateImage(false); // Call generateImage with isRefresh = false
});

refreshBtn.addEventListener("click", function() {
    // Ensure the prompt hasn't changed since the last successful generation
    if (promptInput.value.trim() === lastSuccessfulPrompt && lastSuccessfulPrompt !== "") {
         generateImage(true); // Call generateImage with isRefresh = true
    } else {
        // If prompt changed or no successful generation yet, treat as new generation
        showToast("Prompt changed or no image generated yet. Generating new image.", "info");
        generateImage(false);
    }
});

// Add event listeners to model buttons
document.querySelectorAll(".model-btn").forEach(button => {
    button.addEventListener("click", function () {
        document.querySelectorAll(".model-btn").forEach(btn => btn.classList.remove("active"));
        this.classList.add("active");
    });
});

savePromptBtn.addEventListener("click", function () {
    const prompt = promptInput.value.trim();
    // Ensure saving the prompt that was actually used for the displayed image
    if (!prompt || prompt !== lastSuccessfulPrompt) {
         showToast("Please generate an image with the current prompt first.", 'error');
         return;
    }

    const savedPrompts = JSON.parse(localStorage.getItem(promptCookieName)) || [];
    if (!savedPrompts.includes(prompt)) {
        savedPrompts.unshift(prompt);
        if (savedPrompts.length > maxPrompts) savedPrompts.pop();

        localStorage.setItem(promptCookieName, JSON.stringify(savedPrompts));
        showToast("Prompt saved!");
    } else {
        showToast("Prompt already exists!");
    }
});

downloadBtn.addEventListener("click", function () {
    if (frame.src && frame.src !== window.location.href) { // Check if frame.src is valid and not the base URL
        const link = document.createElement("a");
        link.href = frame.src;
        link.download = "generated_image.png";
        link.click();
        showToast("Image downloaded!");
    } else {
         showToast("No image to download.", 'error');
    }
});

shareBtn.addEventListener("click", function () {
     if (frame.src && frame.src !== window.location.href) { // Check if frame.src is valid
        fetch(frame.src)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                return res.blob();
             })
            .then(blob => {
                const file = new File([blob], "generated_image.png", { type: blob.type });
                if (navigator.share) {
                    navigator.share({
                        files: [file],
                        title: "AI Art Generator",
                        text: `Check out this amazing image I created using the prompt: "${promptInput.value}". Explore more at https://tryq8flix.com/aiGenerator.php`,
                    }).then(() => showToast("Image shared successfully!"))
                      .catch(() => showToast("Image sharing canceled."));
                } else {
                    showToast("Sharing not supported on your browser.");
                }
            });
    } else {
         showToast("No image to share.", 'error');
    }
});

// Helper function to handle historyList messages
const showHistoryMessage = (message) => {
    historyList.style.display = 'block';
    historyList.innerHTML = message;
    setTimeout(() => {
        historyList.style.display = 'none';
    }, 3000);
};

viewHistoryBtn.addEventListener("click", function () {
    const savedPrompts = JSON.parse(localStorage.getItem(promptCookieName)) || [];
    if (savedPrompts.length === 0) {
        showHistoryMessage("<p class='text-center'><i class='fas fa-info-circle mr-2'></i>No prompts found!</p>");
        return;
    }
    
    historyList.style.display = 'block';
    historyList.innerHTML = "<h5><i class='fas fa-history mr-2'></i>Prompt History</h5>";
    savedPrompts.forEach((prompt, index) => {
        const btn = document.createElement("button");
        btn.innerHTML = `<i class="fas fa-redo mr-1"></i> ${prompt}`;
        btn.className = "btn btn-outline-light btn-sm d-block mt-2 text-left w-100";
        btn.style.whiteSpace = "normal";
        btn.style.wordBreak = "break-word";
        btn.onclick = () => {
            promptInput.value = prompt;
            // Clicking history should trigger a new generation, not refresh
            generateImage(false); 
            historyList.style.display = 'none';
        };
        historyList.appendChild(btn);
    });
    
    // Add a close button
    const closeBtn = document.createElement("button");
    closeBtn.innerHTML = `<i class="fas fa-times mr-1"></i> Close`;
    closeBtn.className = "btn btn-outline-secondary btn-sm d-block mt-3 mx-auto";
    closeBtn.onclick = () => {
        historyList.style.display = 'none';
    };
    historyList.appendChild(closeBtn);
});

clearCookiesBtn.addEventListener("click", function () {
    localStorage.removeItem(promptCookieName);
    showToast("Prompts cleared!");
});

promptInput.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        generateImage(false); // Trigger new generation on Enter
    }
});

// Add input event for mobile devices
promptInput.addEventListener("input", function(event) {
    // Reset seed and disable refresh if prompt text changes manually
    if (promptInput.value.trim() !== lastSuccessfulPrompt) {
        currentSeed = 0;
        refreshBtn.disabled = true; // Disable refresh if prompt changes before generating
    }
    if (event.inputType === "insertLineBreak") {
        event.preventDefault();
         generateImage(false); // Trigger new generation on mobile Enter/Go
    }
});