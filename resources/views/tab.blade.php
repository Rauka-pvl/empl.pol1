<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('Service Worker зарегистрирован:', registration);
                }, function(error) {
                    console.log('Service Worker регистрация провалена:', error);
                });
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"
        integrity="sha512-efAcjYoYT0sXxQRtxGY37CKYmqsFVOIwMApaEbrxJr4RwqVVGw8o+Lfh/+59TU07+suZn1BWq4fDl5fdgyCNkw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>

<body style="padding: 0 15px;">
    <style>
        * {
            padding: 0;
            margin: 0;
        }

        .center {
            margin: 0 auto 0 auto;
            text-align: center;
        }

        .center-center {
            margin: auto;
            text-align: center;
        }

        .alert {
            position: fixed;
            top: 25%;
            left: 25%;
            /* transform: translate(-50%, -10%); */
            width: 400px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .alert-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
    <div>
        <div class="d-none">
            <video id="video" width="100%" height="auto" autoplay></video>
        </div>
        <div class="justify-content-center">
            <div class="col-md d-flex">
                <div class="card" style="margin-left: auto; margin-right: auto; width: 45%; border: 0;">
                    <div style="margin: auto">
                        <div id="qrcode"></div>
                    </div>
                    <div class="center" style="margin-top: 1em;">
                        <h4>Сіз өз пікіріңізді қалдыра аласыз!</h4>
                        <h4>Вы можете оставить отзыв!</h4>
                    </div>
                </div>
                <div class="card" style="margin-left: auto; margin-right: auto; width: 45%; border: 0;">
                    <div>
                        <div class="center" style="margin: 0">
                            <h1 id="kab" style="margin-bottom: 0; font-size: 130px;"></h1>
                        </div>
                        <div class="center">
                            <h3 id="sub" style="margin-bottom: 0;"></h3>
                        </div>
                    </div>
                    <div style="margin: 2em 0;" class="center">
                        <input type="hidden" id="id" value="">
                        <button type="submit" id="capture" onclick="gradeKab(this)" name="grade" value="3"
                            class="btn">
                            <img src="storage/smile/3.png" width="80" height="80px">
                        </button>
                        <button type="submit" id="capture" onclick="gradeKab(this)" name="grade" value="2"
                            class="btn">
                            <img src="storage/smile/2.png" width="80" height="80">
                        </button>
                        <button type="submit" id="capture" onclick="gradeKab(this)" name="grade" value="1"
                            class="btn">
                            <img src="storage/smile/1.png" width="80" height="80">
                        </button>
                        <!-- {{-- asset('storage/app/public/icons/smile/1.png') --}}
                        {{-- Storage::url('icons/smile/3.png') --}} -->
                    </div>
                    <div class="center">
                        <h4>Қызмет көрсету сапасын бағалаңыз</h4>
                        <h4>Оцените качество обслуживания</h4>
                    </div>
                </div>
            </div>
            <div class="col-md d-flex">
                <div style="margin: 0 auto; font-size: 10px;">
                    При оставлений оценки вы соглашаетесь с фотофиксацией.
                </div>
            </div>
        </div>
    </div>
    <script>
        var currentDate = new Date();

        $(function() {
            // получение доступа к камере и настройка видеопотока
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(function(stream) {
                    var video = document.getElementById('video');
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(error) {
                    console.log(error);
                });
        });

        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const KabId = params.get('KabId');

        if (!KabId && localStorage.getItem("KabId")) {
            alert('Нет ID устройства!!!');
        } else if (KabId) {
            localStorage.setItem("KabId", KabId);
        }
        let Kab = localStorage.getItem("KabId").split('-');

        let kab_num = document.getElementById('kab');
        let sub = document.getElementById('sub');
        let id = document.getElementById('id');
        $.ajax({
            type: 'GET',
            url: '/api/getKabInfo',
            data: {
                id: Kab[0],
                corpus: Kab[1],
            },
            success: function(response) {
                if (response) {
                    kab_num.textContent = response['kab'];
                    sub.textContent = response['sub'];
                    id.value = response['id'];

                    $.ajax({
                        type: 'GET',
                        url: '/api/checkTab',
                        data: {
                            kab: Kab[0],
                            corpus: Kab[1],
                        },
                        success: function(response) {
                            if (response) {
                                console.log(true);
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                    // $.ajax({
                    //     type: 'GET',
                    //     url: 'http://192.168.120.186:8000/api/get_json',
                    //     data: {
                    //         cab: Kab[0],
                    //         corpus: Kab[1]
                    //     },
                    //     success: function(response2) {
                    //         if (response2) {
                    //             let time = findTime(response2);

                    //             let hours = currentDate.getHours();
                    //             let minutes = currentDate.getMinutes();
                    //             // let seconds = currentDate.getSeconds();
                    //             let formattedHours = (hours < 10) ? `0${hours}` : hours;
                    //             let formattedMinutes = (minutes < 10) ? `0${minutes}` : minutes;

                    //             let currentTime = formattedHours + ':' + formattedMinutes;
                    //             console.log(currentTime);
                    //             console.log(time);
                    //             console.log(response2);
                    //             if (Array.isArray(time)) {
                    //                 time.forEach(e => {
                    //                     if (currentTime > e['t'].split(" ")[0] &&
                    //                         currentTime < e['t'].split(" ")[1]) {
                    //                         sub.textContent = response['sub'] + ': ' +
                    //                             e['name'];
                    //                     }
                    //                 });
                    //             } else {
                    //                 if (currentTime > time['t'].split(" ")[0] && currentTime <
                    //                     time['t'].split(" ")[1]) {
                    //                     sub.textContent = response['sub'] + ': ' + time['name'];
                    //                 }
                    //             }
                    //         }
                    //     },
                    //     error: function(error) {
                    //         console.log(error);
                    //     }
                    // });
                }
            },
            error: function(error) {
                console.log(error);
            }
        });

        var text = "https://empl.pol1pvl.kz/qrKab/" + Kab;

        // Создайте новый экземпляр QRCode
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: text,
            width: 300,
            height: 300,
        });

        function gradeKab(button) {
            var user_id = document.getElementById('id');
            var canvas = document.createElement('canvas');
            canvas.width = 640;
            canvas.height = 480;
            var context = canvas.getContext('2d');
            var video = document.getElementById('video');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            var dataUrl = canvas.toDataURL();
            $.ajax({
                type: 'POST',
                url: '/api/gradeKab',
                data: {
                    image: dataUrl,
                    kab_id: id.value,
                    grade: button.value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response) {
                        let grades = JSON.parse(localStorage.getItem('grade')) || [];
                        if (grades.length > 1) {
                            grades = grades.filter(g => !returnGrade(g));
                            localStorage.removeItem('grade');
                        }
                        console.log(response);
                        showAlertGrade();

                        $.ajax({
                            type: 'POST',
                            url: 'https://script.google.com/macros/s/AKfycbzY7oU4xPe0RAsum1BNaGq6pmH03H56YilABlNJwLYhYq7LuFzqI34tNK8VUEVqygLf/exec',
                            data: {
                                kab: KabId,
                                grade: button.value,
                                sub: sub.textContent,
                                date: currentDate.toLocaleString()
                            },
                            success: function(responseT) {
                                if (responseT) {
                                    console.log(responseT);
                                    console.info('sheet add');
                                }
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                    }
                },
                error: function(error) {
                    let grade = JSON.parse(localStorage.getItem('grade')) || [];
                    let data = [];
                    let data1 = {
                        url: '/api/gradeKab',
                        image: dataUrl,
                        kab_id: id.value,
                        grade: button.value,
                        doctor: sub.innerText.split(": ")[1],
                        createD: getCurrentDate(),
                        updateD: getCurrentDate()
                    };
                    let data2 = {
                        url: 'https://script.google.com/macros/s/AKfycbzY7oU4xPe0RAsum1BNaGq6pmH03H56YilABlNJwLYhYq7LuFzqI34tNK8VUEVqygLf/exec',
                        kab: KabId,
                        grade: button.value,
                        sub: sub.textContent,
                        date: getCurrentDate()
                    };

                    data.push(data1);
                    data.push(data2);
                    grade.push(data);
                    // console.log(data);
                    // console.log(grade);
                    localStorage.setItem('grade', JSON.stringify(grade));
                    showAlertGrade();
                    console.log(error);
                }
            });
        }

        function returnGrade(array) {
            $.ajax({
                type: 'POST',
                url: '/api/gradeKab',
                data: {
                    image: array[0]['image'],
                    kab_id: array[0]['kab_id'],
                    grade: array[0]['grade'],
                    doctor: array[0]['doctor'],
                    createD: array[0]['createD'],
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response) {
                        console.log('returnGrade: ' + response);

                        $.ajax({
                            type: 'POST',
                            url: 'https://script.google.com/macros/s/AKfycbzY7oU4xPe0RAsum1BNaGq6pmH03H56YilABlNJwLYhYq7LuFzqI34tNK8VUEVqygLf/exec',
                            data: {
                                kab: array[1]['kab'],
                                grade: array[1]['grade'],
                                sub: array[1]['sub'],
                                date: array[1]['date']
                            },
                            success: function(responseT) {
                                if (responseT) {
                                    console.info('returnGrade: sheet add');
                                    return true;
                                }
                            },
                            error: function(error) {
                                console.log(error);
                            }
                        });
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function getCurrentDate() {
            let currentDate = new Date();

            let year = currentDate.getFullYear();
            let month = String(currentDate.getMonth() + 1).padStart(2,
                '0'); // добавляем ведущий ноль, если месяц состоит из одной цифры
            let day = String(currentDate.getDate()).padStart(2, '0');
            let hours = String(currentDate.getHours()).padStart(2, '0');
            let minutes = String(currentDate.getMinutes()).padStart(2, '0');
            let seconds = String(currentDate.getSeconds()).padStart(2, '0');

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        function showAlertGrade() {
            let alert1 = document.createElement('div');
            alert1.classList.add('alert');

            // Создаем новый элемент p для текста alert
            let aletText = document.createElement('p');
            aletText.style.backgroundColor = 'white';
            aletText.innerText = 'Сіз сәтті бағаладыңыз!';

            let alertText1 = document.createElement('p');
            alertText1.style.backgroundColor = 'white';
            alertText1.innerText = 'Вы успешно оценили!';

            // Создаем новый элемент a для кнопки "OK"
            let alertButton1 = document.createElement('a');
            alertButton1.classList.add('alert-button');
            alertButton1.innerText = 'OK';

            // Добавляем обработчик событий на кнопку "OK", чтобы скрыть alert при нажатии
            alertButton1.addEventListener('click', () => {
                alert1.remove();
            });

            // Добавляем alert на страницу
            alert1.appendChild(aletText);
            alert1.appendChild(alertText1);
            alert1.appendChild(alertButton1);
            document.body.appendChild(alert1);

            setTimeout(() => {
                alert1.remove();
            }, 2000);
        }

        setInterval(function() {
            $.ajax({
                type: 'GET',
                url: '/api/checkTab',
                data: {
                    kab: Kab[0],
                    corpus: Kab[1],
                },
                success: function(response) {
                    if (response) {
                        console.log(true);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }, 1800000);

        setTimeout(function() {
            location.reload();
        }, 3600000);
    </script>

</body>

</html>
