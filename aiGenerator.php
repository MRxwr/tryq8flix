<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>AI Art Generator</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #6b3acb, #9c2cf2, #4b8cfe, #2fe0bf);
            background-size: 400% 400%;
            animation: moveBackground 20s ease infinite;
            min-height: 100vh;
        }

        @keyframes moveBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .page-title {
            color: #fff;
            text-align: center;
            font-weight: 700;
            margin-top: 30px;
            letter-spacing: 1px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: fadeIn 1s ease-in;
        }

        .page-subtitle {
            color: #fff;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
            margin-top: 30px;
            letter-spacing: 1px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h6 {
            color: rgba(255, 255, 255, 0.9);
            font-style: italic;
            margin-bottom: 30px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
        }

        .generator-card {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 25px;
            margin: 30px auto;
            max-width: 800px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .generator-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .model-btn {
            margin: 5px;
            border-radius: 30px;
            padding: 8px 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .model-btn:before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: all 0.6s;
        }

        .model-btn:hover:before {
            left: 100%;
        }

        .model-btn.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .btn {
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn-primary {
            background: linear-gradient(45deg, #4e73df, #2e59d9);
            border: none;
        }

        .btn-success {
            background: linear-gradient(45deg, #1cc88a, #13855c);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
            border: none;
            color: #2e2f37;
        }

        .btn-info {
            background: linear-gradient(45deg, #36b9cc, #258391);
            border: none;
        }

        .btn-secondary {
            background: linear-gradient(45deg, #858796, #60616f);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(45deg, #e74a3b, #be2617);
            border: none;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        #returnImage {
            transition: all 0.5s ease;
        }

        #frame {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        #frame:hover {
            transform: scale(1.02);
        }

        .section-divider {
            border-color: rgba(255, 255, 255, 0.2);
            margin: 20px 0;
        }

        footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px 0;
            color: white;
            background-color: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
        }

        footer a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        footer a:hover {
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
            transform: translateY(-3px);
        }

        #loading {
            display: none; /* Keep this to hide initially */
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1100;
            flex-direction: column; /* These properties only apply when display is flex */
            justify-content: center; /* These properties only apply when display is flex */
            align-items: center; /* These properties only apply when display is flex */
        }

        #loading .spinner {
            border: 8px solid rgba(255, 255, 255, 0.1);
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        #loading p {
            font-size: 1.2rem;
            margin-top: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #historyList {
            display: none;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        #historyList h5 {
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
        }

        .feature-title {
            font-size: 1.2rem;
            margin: 15px 0 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
        }

        .icon-input {
            padding-left: 40px;
        }

        .tooltip-toggle {
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            margin-left: 5px;
        }
    </style>
  </head>
  <body>
    <h1 class="page-title">AI Art Generator</h1>
    <h6 class="page-subtitle">Transform Your Imagination Into Stunning Art</h6>
    <div class="container-fluid">
        <div class="row text-center generator-card">
          <div class="col-12 mb-3">
            <h3 class="text-center mb-4"><i class="fas fa-paint-brush mr-2"></i>Create Your Masterpiece</h3>
          </div>
            <div class="col-12 mb-3">
                <div class="input-icon-wrapper">
                    <i class="fas fa-magic input-icon"></i>
                    <input name="prompt" id="prompt" value="" class="form-control icon-input" placeholder="Describe your art (e.g., a futuristic city with flying cars)" autocomplete="off">
                </div>
            </div>
            
            <div class="col-md-6 col-12 pt-2">
                <p class="text-left mb-1"><i class="fas fa-arrows-alt-h mr-1"></i> Width</p>
                <input type="number" id="imgWidth" class="form-control" placeholder="Width (e.g. 1080)" min="256" max="2048" value="1080">
            </div>
            <div class="col-md-6 col-12 pt-2">
                <p class="text-left mb-1"><i class="fas fa-arrows-alt-v mr-1"></i> Height</p>
                <input type="number" id="imgHeight" class="form-control" placeholder="Height (e.g. 1080)" min="256" max="2048" value="1080">
            </div>
            
            <div class="col-12 pt-4 text-center">
                <p class="feature-title"><i class="fas fa-robot mr-1"></i> Select Model <span class="tooltip-toggle" data-toggle="tooltip" title="Normal: Better quality but slower. Fast: Quick generation with good quality."><i class="fas fa-question-circle"></i></span></p>
                <button class="btn btn-outline-light model-btn" data-model="normal">Normal</button>
                <button class="btn btn-outline-light model-btn" data-model="turbo">Fast</button>
            </div>
            
            <div class="col-12 pt-4 text-center">
                <button id="submitBtn" class="btn btn-primary w-100"><i class="fas fa-bolt mr-2"></i> Generate Image</button>
            </div>
            
            <div id="toastContainer" class="col-12 pt-2 text-center"></div>
            
            <div class="col-12"><hr class="section-divider"></div>
            
            <div class="col-12 pt-3" id="returnImage">
                <img id="frame" src="" alt="Your generated image will appear here">
            </div>
            
            <div class="col-12 pt-4">
                <div class="row">
                    <div class="col-md-6 col-12 mb-2">
                        <button id="downloadBtn" class="btn btn-success w-100" disabled><i class="fas fa-download mr-1"></i> Download</button>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <button id="shareBtn" class="btn btn-warning w-100" disabled><i class="fas fa-share-alt mr-1"></i> Share</button>
                    </div>
                </div>
                <button id="savePromptBtn" class="btn btn-info w-100 mb-3 mt-2" disabled><i class="fas fa-bookmark mr-1"></i> Save Prompt</button>
                
                <div class="row mt-3">
                    <div class="col-md-6 col-12 mb-2">
                        <button id="viewHistoryBtn" class="btn btn-secondary w-100"><i class="fas fa-history mr-1"></i> Prompts History</button>
                    </div>
                    <div class="col-md-6 col-12 mb-2">
                        <button id="clearCookiesBtn" class="btn btn-danger w-100"><i class="fas fa-trash-alt mr-1"></i> Clear Prompts</button>
                    </div>
                </div>
                
                <div id="historyList" class="mt-3"></div>
            </div>
        </div>
    </div>
    
    <footer>
        <p>Share this website:</p>
        <div class="mb-3">
            <a href="https://www.facebook.com/sharer/sharer.php?u=https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-facebook-square fa-2x"></i></a>
            <a href="https://twitter.com/intent/tweet?url=https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
            <a href="https://wa.me/?text=Check out this amazing AI Art Generator: https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-whatsapp-square fa-2x"></i></a>
        </div>
        <p>Courtesy of <a href="https://tryq8flix.com" target="_blank">TRYQ8FLiX</a></p>
    </footer>
    
    <div id="loading">
        <div class="spinner"></div>
        <p>Creating Your Masterpiece... Please Wait</p>
    </div>
    
    <script>
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
        const viewHistoryBtn = document.getElementById("viewHistoryBtn");
        const clearCookiesBtn = document.getElementById("clearCookiesBtn");
        const frame = document.getElementById("frame");
        const historyList = document.getElementById("historyList");
        const toastContainer = document.getElementById("toastContainer");
        const loading = document.getElementById("loading");

        const maxPrompts = 5;
        const promptCookieName = "savedPrompts";

        const showToast = (message) => {
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
            toast.className = "toast align-items-center text-white bg-success border-0 show";
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
                    <div class="toast-body"><i class="fas fa-check-circle mr-2"></i>${message}</div>
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

        submitBtn.addEventListener("click", function () {
            const prompt = promptInput.value.trim();
            const selectedModel = document.querySelector(".model-btn.active")?.getAttribute("data-model") || "normal";
            const width = document.getElementById("imgWidth").value || 1080;
            const height = document.getElementById("imgHeight").value || 1080;
            if (!prompt) {
                showToast("Please enter a prompt first!");
                return;
            }

            loading.style.display = "flex"; // Set display to flex here when starting
            const modelParam = selectedModel !== "normal" ? `&model=${selectedModel}` : "";
            frame.src = `https://image.pollinations.ai/prompt/${encodeURIComponent(prompt)}?width=${width}&height=${height}&nologo=true${modelParam}`;

            frame.onload = () => {
                loading.style.display = "none"; // Set display back to none here when finished
                downloadBtn.disabled = false;
                savePromptBtn.disabled = false;
                shareBtn.disabled = false;
                showToast("Image generated successfully!");
            };
            // Add error handling in case the image fails to load
            frame.onerror = () => {
                loading.style.display = "none";
                showToast("Error generating image. Please try again.");
                // Optionally disable buttons again or keep them enabled
                // downloadBtn.disabled = true;
                // savePromptBtn.disabled = true;
                // shareBtn.disabled = true;
            };
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
            if (!prompt) return;

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
            if (frame.src) {
                const link = document.createElement("a");
                link.href = frame.src;
                link.download = "generated_image.png";
                link.click();
                showToast("Image downloaded!");
            }
        });

        shareBtn.addEventListener("click", function () {
            if (frame.src) {
                fetch(frame.src)
                    .then(res => res.blob())
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
                    submitBtn.click();
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
                submitBtn.click();
            }
        });
        
        // Add input event for mobile devices
        promptInput.addEventListener("input", function(event) {
            if (event.inputType === "insertLineBreak") {
                event.preventDefault();
                submitBtn.click();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>


