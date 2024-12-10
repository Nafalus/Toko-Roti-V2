<?php
session_start();
$koneksi = mysqli_connect("localhost", "root", "", "toko_roti");

// Periksa apakah pengguna telah login sebagai admin, jika tidak, alihkan ke halaman login
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: landingPage.php");
//     exit();
// }

$user_name = $_SESSION['user_name'];
$qryjenis = "SELECT * FROM jenis_roti";
$resultJenis = mysqli_query($koneksi, $qryjenis);

$error_message = '';
$success_message = '';

// Handle form submissions for create, update, and delete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $nama_produk = $_POST['nama_produk'] ?? '';
        $harga = $_POST['harga'] ?? '';
        $id_jenis = $_POST['id_jenis'] ?? '';
        $gambar = $_FILES['gambar'] ?? '';

        if ($gambar['name']) {
            $target_dir = "../assets/image/produk/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($gambar['name']);
            if (move_uploaded_file($gambar['tmp_name'], $target_file)) {
                $relative_path = "/assets/image/produk/" . basename($gambar['name']);
                $query = "INSERT INTO produk (nama_produk, harga, id_jenis, gambar) VALUES ('$nama_produk', '$harga', '$id_jenis', '$relative_path')";
                if (mysqli_query($koneksi, $query)) {
                    $success_message = "Produk berhasil ditambahkan.";
                } else {
                    $error_message = "Gagal menambahkan produk.";
                }
            } else {
                $error_message = "Gagal mengupload gambar.";
            }
        } else {
            $error_message = "Gambar harus diupload.";
        }
    } elseif ($action == 'update') {
        $id_produk = $_POST['id_produk'] ?? '';
        $nama_produk = $_POST['nama_produk'] ?? '';
        $harga = $_POST['harga'] ?? '';
        $id_jenis = $_POST['id_jenis'] ?? '';
        $gambar = $_FILES['gambar'] ?? '';

        if ($gambar['name']) {
            $target_dir = "assets/image/produk/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($gambar['name']);
            if (move_uploaded_file($gambar['tmp_name'], $target_file)) {
                $query = "UPDATE produk SET nama_produk = '$nama_produk', harga = '$harga', id_jenis = '$id_jenis', gambar = '{$gambar['name']}' WHERE id_produk = $id_produk";
            } else {
                $error_message = "Gagal mengupload gambar.";
            }
        } else {
            $query = "UPDATE produk SET nama_produk = '$nama_produk', harga = '$harga', id_jenis = '$id_jenis' WHERE id_produk = $id_produk";
        }

        if (isset($query) && !empty($query)) {
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Produk berhasil diupdate.";
            } else {
                $error_message = "Gagal mengupdate produk.";
            }
        } else {
            $error_message = "Terjadi kesalahan dalam memproses permintaan.";
        }
    } elseif ($action == 'delete') {
        $id_produk = $_POST['id_produk'] ?? '';

        $query = "DELETE FROM produk WHERE id_produk = $id_produk";
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Produk berhasil dihapus.";
        } else {
            $error_message = "Gagal menghapus produk.";
        }
    }
}

// Fetch data for listing
$resultProduk = mysqli_query($koneksi, "SELECT produk.*, jenis_roti.nama AS nama_jenis FROM produk JOIN jenis_roti ON produk.id_jenis = jenis_roti.id_jenis");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Manage Produk - Toko Roti</title>
    <style>
        .floating-form {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            max-height: 90vh;
            overflow-y: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: none;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-green-800 text-white">
            <div class="p-4">
                <img class="h-8 w-auto mx-auto" src="assets/image/WhatsApp Image 2024-05-01 at 11.37.02_c6c61df6.jpg" alt="Your Company">
                <h2 class="mt-6 text-center text-xl font-bold">Admin Dashboard</h2>
            </div>
            <nav class="mt-10">
                <a href="dasbordAdmin.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-green-600">Dashboard</a>
                <a href="manageProduk.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-green-600">Manage Products</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-green-800">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-700">Selamat datang, <?php echo $user_name; ?>!</h1>
                </div>
                <div class="flex items-center">
                    <a href="landingPage.php" class="text-gray-700 hover:text-green-600">
                        <span class="font-medium">Logout</span>
                    </a>
                </div>
            </header>

            <!-- Main content area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <!-- Error and success messages -->
                <?php if ($success_message) : ?>
                    <div id="success_message" class="absolute top-0 left-0 right-0 mx-auto w-full bg-green-500 text-white p-4 rounded mb-4 max-w-4xl"><?php echo $success_message; ?></div>
                    <script>
                        setTimeout(function() {
                            document.getElementById('success_message').style.display = 'none';
                        }, 3000);
                    </script>
                <?php endif; ?>
                <?php if ($error_message) : ?>
                    <div class="bg-red-500 text-white p-4 rounded mb-4 max-w-md mx-auto"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form tambah produk -->
                <div class="mt-8 max-w-md mx-auto">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Tambah Produk</h3>
                        <form action="manageProduk.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="action" value="create">
                            <div>
                                <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk:</label>
                                <input type="text" name="nama_produk" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="harga" class="block text-sm font-medium text-gray-700">Harga:</label>
                                <input type="number" name="harga" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="id_jenis" class="block text-sm font-medium text-gray-700">Jenis Kue:</label>
                                <select name="id_jenis" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                    <?php while ($jenis = mysqli_fetch_assoc($resultJenis)) : ?>
                                        <option value="<?php echo $jenis['id_jenis']; ?>"><?php echo $jenis['nama']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar:</label>
                                <input type="file" name="gambar" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="py-2 px-4 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">Tambah Produk</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar produk -->
                <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Daftar Produk</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php while ($produk = mysqli_fetch_assoc($resultProduk)) : ?>
                                <div class="bg-white p-4 rounded-lg shadow-md flex flex-col">
                                    <img src="<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_produk']; ?>" class="w-full h-48 object-cover mb-4 rounded-lg">
                                    <div class="flex flex-col flex-grow">
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2"><?php echo $produk['nama_produk']; ?></h4>
                                        <p class="text-gray-600 mb-4"><?php echo $produk['nama_jenis']; ?></p>
                                        <p class="text-lg font-semibold text-gray-800 mb-4">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                                    </div>
                                    <div class="flex justify-between mt-auto">
                                        <button class="py-2 px-4 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700" onclick="editProduk(<?php echo $produk['id_produk']; ?>, '<?php echo $produk['nama_produk']; ?>', <?php echo $produk['harga']; ?>, <?php echo $produk['id_jenis']; ?>)">Edit</button>
                                        <form action="manageProduk.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">
                                            <button type="submit" class="py-2 px-4 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Floating form for editing -->
    <div id="editForm" class="floating-form ">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Edit Produk</h3>
        <form id="editFormContent" action="manageProduk.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_produk" id="edit_id_produk">
            <div>
                <label for="edit_nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk:</label>
                <input type="text" name="nama_produk" id="edit_nama_produk" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div>
                <label for="edit_harga" class="block text-sm font-medium text-gray-700">Harga:</label>
                <input type="number" name="harga" id="edit_harga" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div>
                <label for="edit_id_jenis" class="block text-sm font-medium text-gray-700">Jenis Kue:</label>
                <select name="id_jenis" id="edit_id_jenis" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                    <?php
                    mysqli_data_seek($resultJenis, 0); // Reset pointer to the first row
                    while ($jenis = mysqli_fetch_assoc($resultJenis)) : ?>
                        <option value="<?php echo $jenis['id_jenis']; ?>"><?php echo $jenis['nama']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Gambar (opsional):</label>
                <input type="file" name="gambar" id="edit_gambar" class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="text-right">
                <button type="submit" class="py-2 px-4 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700">Update Produk</button>
                <button type="button" class="py-2 px-4 bg-gray-600 text-white rounded-md shadow-sm hover:bg-gray-700" onclick="closeEditForm()">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        function editProduk(id_produk, nama_produk, harga, id_jenis) {
            document.getElementById('edit_id_produk').value = id_produk;
            document.getElementById('edit_nama_produk').value = nama_produk;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_id_jenis').value = id_jenis;
            document.getElementById('editForm').style.display = 'block';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>

</html>
