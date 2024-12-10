<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lokasi Toko</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/d3b2ed7825.js" crossorigin="anonymous"></script>
  <style>
    #map {
      height: 500px;
      width: 100%;
      border-radius: 0.5rem;
    }
  </style>
</head>

<body class="bg-gray-100">
  <nav class="bg-blue-800 fixed w-full">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <img class="h-8 w-auto" src="../assets/image/WhatsApp Image 2024-05-01 at 11.37.02_c6c61df6.jpg" alt="Your Company">
          </div>
          <div class="hidden sm:block ml-4">
            <div class="flex space-x-4">
              <a href="landingPage.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-sm font-medium">Home</a>
              <a href="jenisKue.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-sm font-medium">Catalog</a>
              <a href="#about" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-sm font-medium">About Us</a>
              <a href="#contact" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-sm font-medium">Contact</a>
              <a href="ourLocation.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 text-sm font-medium">Our Location</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <div class="pt-16">
    <div class="container mx-auto py-8">
      <h1 class="text-3xl font-bold text-center text-green-800 mb-4">Lokasi Toko Kami</h1>
      <p class="text-center text-gray-700 mb-8">Temukan lokasi toko kami dengan mudah menggunakan peta interaktif di bawah ini.</p>
      <div id="map" class="shadow-lg mx-auto"></div>
    </div>
  </div>

  <footer class="bg-white shadow-md mt-8">
    <?php include '../utiliti/footer.php'; ?>
  </footer>

  <script>
    function initMap() {
      // Koordinat toko
      const tokoLocation = { lat: -7.286610, lng: 112.781532 };
      // Membuat map
      const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: tokoLocation,
      });
      // Menambahkan marker
      const marker = new google.maps.Marker({
        position: tokoLocation,
        map: map,
        title: "Toko Kami",
      });
    }
  </script>
  <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmp_hSrcaIL4AU-WfruH57C_NgjuL41fA&callback=initMap"></script>
</body>

</html>
