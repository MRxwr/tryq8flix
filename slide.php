<!DOCTYPE html>
<html dir="rtl">
<head>
    <style>
        /* [Previous CSS styles remain the same until nav-buttons] */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }
        .slideshow-container {
            width: 100vw;
            height: 100vh;
            position: relative;
        }
        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .slide.active {
            opacity: 1;
        }
        .content-box {
            background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
            padding: 40px;
            border-radius: 15px;
            max-width: 800px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .slide1 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        .slide2 { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
        .slide3 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .slide4 { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
        .slide5 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }

        h1 {
            color: #2c3e50;
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            background: linear-gradient(90deg, #2c3e50, #3498db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h2 {
            color: #2980b9;
            margin-top: 20px;
        }
        h3 {
            color: #e67e22;
        }
        ul {
            list-style-type: none;
            padding-right: 20px;
        }
        li {
            margin: 15px 0;
            position: relative;
            color: #34495e;
            padding: 10px 25px 10px 10px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            transition: transform 0.2s;
        }
        li:hover {
            transform: translateX(-5px);
            background: rgba(255, 255, 255, 0.9);
        }
        li:before {
            content: "🌾";
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
        }
        .highlight {
            background: linear-gradient(90deg, #00C9FF 0%, #92FE9D 100%);
            padding: 15px;
            border-radius: 10px;
            color: white;
            text-align: center;
            font-size: 1.2em;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        /* Updated nav-buttons styles */
        .nav-buttons {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            gap: 20px;
        }
        .nav-btn {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.5em;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s, background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .nav-btn:hover {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 1);
        }

        .slide-counter {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 1.1em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .emoji-icon {
            font-size: 2em;
            margin-bottom: 10px;
            text-align: center;
            display: block;
        }
    </style>
</head>
<body>
    <!-- [Previous slide content remains exactly the same] -->
    <div class="slideshow-container">
        <div class="slide slide1 active">
            <div class="content-box">
                <span class="emoji-icon">🌾</span>
                <h1>إطلاق منتج جديد</h1>
                <h2>خبز الشوفان والألياف</h2>
                <div class="highlight">
                    "خبز صحي أكثر بخيارات متنوعة"
                </div>
                <h3>أبرز مميزات المنتج:</h3>
                <ul>
                    <li>مكونات عالية الجودة: دقيق ممتاز ممزوج بـ 20% شوفان مع ألياف الشوفان</li>
                    <li>مرونة الإنتاج: خيارات متعددة بأحجام مختلفة</li>
                    <li>منتج صحي عالي الطلب: يلبي احتياجات المستهلكين الواعين صحياً</li>
                </ul>
            </div>
        </div>

        <div class="slide slide2">
            <div class="content-box">
                <span class="emoji-icon">📈</span>
                <h1>لماذا نطلق هذا المنتج الآن؟</h1>
                <ul>
                    <li>منتج مربح: ارتفاع الطلب على المنتجات الصحية</li>
                    <li>تنويع المنتجات: زيادة الخيارات المتاحة</li>
                    <li>ميزة تنافسية: المورد الوحيد بتكلفة تنافسية</li>
                    <li>زيادة الاستفادة من الطاقة الإنتاجية</li>
                </ul>
            </div>
        </div>

        <div class="slide slide3">
            <div class="content-box">
                <span class="emoji-icon">🌟</span>
                <h1>فوائد خبز الشوفان</h1>
                <h2>ما الذي يجعل خبز الشوفان منتجاً مميزاً؟</h2>
                <ul>
                    <li>مصدر غني بالعناصر الغذائية: بروتين، حديد، كالسيوم</li>
                    <li>سهل الهضم ويعزز الراحة الهضمية</li>
                    <li>يدعم صحة القلب والجهاز الهضمي</li>
                    <li>يزيد الشعور بالشبع</li>
                    <li>يحسن جودة النظام الغذائي</li>
                </ul>
            </div>
        </div>

        <div class="slide slide4">
            <div class="content-box">
                <span class="emoji-icon">💪</span>
                <h1>التأثير على الجودة والربحية</h1>
                <h2>تعزيز الجودة:</h2>
                <ul>
                    <li>تحسين قوام الخبز وطراوته</li>
                    <li>إطالة عمر الصلاحية</li>
                    <li>زيادة القيمة الغذائية</li>
                </ul>
                <h2>الربحية والنمو المتوقع:</h2>
                <ul>
                    <li>ارتفاع المبيعات</li>
                    <li>توسيع شريحة العملاء</li>
                    <li>خفض التكاليف</li>
                </ul>
            </div>
        </div>

        <div class="slide slide5">
            <div class="content-box">
                <span class="emoji-icon">✨</span>
                <h1>الرسالة الختامية</h1>
                <div class="highlight" style="font-size: 1.5em; margin: 30px 0;">
                    "خبز الشوفان – الخيار المثالي بين الصحة والجودة والربحية"
                </div>
                <h2 style="text-align: center; color: #2c3e50;">معاً نحو نجاح جديد لشركتنا!</h2>
            </div>
        </div>
    </div>

    <div class="slide-counter">شريحة <span id="current">1</span> من 5</div>
    
    <!-- Updated navigation buttons -->
    <div class="nav-buttons">
        <button class="nav-btn" onclick="nextSlide()">◀️</button>
        <button class="nav-btn" onclick="prevSlide()">▶️</button>
    </div>

    <script>
        let currentSlide = 1;
        const totalSlides = 5;

        function showSlide(n) {
            const slides = document.querySelectorAll('.slide');
            
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('active');
            });

            // Show current slide
            slides[n-1].classList.add('active');
            
            // Update counter
            document.getElementById('current').textContent = n;
            
            // Update current slide number
            currentSlide = n;
        }

        function nextSlide() {
            if (currentSlide < totalSlides) {
                showSlide(currentSlide + 1);
            }
        }

        function prevSlide() {
            if (currentSlide > 1) {
                showSlide(currentSlide - 1);
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                prevSlide();
            } else if (e.key === 'ArrowLeft') {
                nextSlide();
            }
        });
    </script>
</body>
</html>