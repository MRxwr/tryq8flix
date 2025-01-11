<!DOCTYPE html>
<html dir="rtl">
<head>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        
        /* A4 size in pixels (assuming 96 DPI) */
        .slide {
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto 30px;
            padding: 40px;
            box-sizing: border-box;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        /* Gradient backgrounds for each slide */
        #slide1 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        #slide2 { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
        #slide3 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        #slide4 { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
        #slide5 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }

        .content-box {
            background: linear-gradient(to bottom right, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
            border-radius: 15px;
            padding: 40px;
            margin: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            height: calc(100% - 80px);
            overflow: auto;
        }

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
            font-size: 1.8em;
        }

        h3 {
            color: #e67e22;
            font-size: 1.5em;
        }

        ul {
            list-style-type: none;
            padding-right: 20px;
        }

        li {
            margin: 15px 0;
            position: relative;
            color: #34495e;
            padding: 15px 25px 15px 15px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            font-size: 1.2em;
            transition: transform 0.2s;
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
            padding: 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
            font-size: 1.4em;
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .emoji-icon {
            font-size: 3em;
            text-align: center;
            display: block;
            margin: 20px 0;
        }

        @media print {
            body {
                padding: 0;
                background: none;
            }
            .slide {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<div class="slide" id="slide1">
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

<div class="slide" id="slide2">
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

<div class="slide" id="slide3">
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

<div class="slide" id="slide4">
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

<div class="slide" id="slide5">
    <div class="content-box">
        <span class="emoji-icon">✨</span>
        <h1>الرسالة الختامية</h1>
        <div class="highlight" style="font-size: 1.8em; margin: 40px 0;">
            "خبز الشوفان – الخيار المثالي بين الصحة والجودة والربحية"
        </div>
        <h2 style="text-align: center; color: #2c3e50; font-size: 2em;">معاً نحو نجاح جديد لشركتنا!</h2>
    </div>
</div>

</body>
</html>