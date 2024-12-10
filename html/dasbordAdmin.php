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
$result = mysqli_query($koneksi, $qryjenis);

$error_message = '';
$success_message = '';

// Handle form submissions for create, update, and delete actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'create') {
        $nama = $_POST['nama'] ?? '';
        $gambar = $_FILES['gambar'] ?? '';

        if ($gambar['name']) {
            $target_dir = "assets/image/jeniskue/";
            $target_file = $target_dir . basename($gambar['name']);
            if (move_uploaded_file($gambar['tmp_name'], $target_file)) {
                $relative_path = "/assets/image/jeniskue/" . basename($gambar['name']);
                $query = "INSERT INTO jenis_roti (nama, gambar) VALUES ('$nama','$relative_path')";
                if (mysqli_query($koneksi, $query)) {
                    $success_message = "Jenis kue berhasil ditambahkan.";
                } else {
                    $error_message = "Gagal menambahkan jenis kue.";
                }
            } else {
                $error_message = "Gagal mengupload gambar.";
            }
        } else {
            $error_message = "Gambar harus diupload.";
        }
    } elseif ($action == 'update') {
        $id_jenis = $_POST['id_jenis'] ?? '';
        $nama = $_POST['nama'] ?? '';
        $gambar = $_FILES['gambar'] ?? '';

        if ($gambar['name']) {
            $target_dir = "../assets/image/jeniskue/";
            $target_file = $target_dir . basename($gambar['name']);
            if (move_uploaded_file($gambar['tmp_name'], $target_file)) {
                $query = "UPDATE jenis_roti SET nama = '$nama', gambar = '{$gambar['name']}' WHERE id_jenis = $id_jenis";
            } else {
                $error_message = "Gagal mengupload gambar.";
            }
        } else {
            $query = "UPDATE jenis_roti SET nama = '$nama' WHERE id_jenis = $id_jenis";
        }

        if (isset($query) && !empty($query)) {
            if (mysqli_query($koneksi, $query)) {
                $success_message = "Jenis kue berhasil diupdate.";
            } else {
                $error_message = "Gagal mengupdate jenis kue.";
            }
        } else {
            $error_message = "Terjadi kesalahan dalam memproses permintaan.";
        }
    } elseif ($action == 'delete') {
        $id_jenis = $_POST['id_jenis'] ?? '';

        $query = "DELETE FROM jenis_roti WHERE id_jenis = $id_jenis";
        if (mysqli_query($koneksi, $query)) {
            $success_message = "Jenis kue berhasil dihapus.";
        } else {
            $error_message = "Gagal menghapus jenis kue.";
        }
    }
}

// Fetch data for listing
$result = mysqli_query($koneksi, "SELECT * FROM jenis_roti");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Admin Dashboard - Toko Roti</title>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white">
            <div class="p-4">
            <img class="h-8 w-auto" src="../assets/image/WhatsApp Image 2024-05-01 at 11.37.02_c6c61df6.jpg" alt="Your Company">
                <h2 class="mt-6 text-center text-xl font-bold">Admin Dashboard</h2>
            </div>
            <nav class="mt-10">
                <a href="dasbordAdmin.php"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-green-600">Dashboard</a>
                <a href="manageProduk.php"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-green-600">Manage Products</a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-green-800">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-700">Selamat datang, <?php echo $user_name; ?>!</h1>
                </div>
                <div class="flex items-center">
                    <a href="login.php" class="text-gray-700 hover:text-green-600">
                        <span class="font-medium">Logout</span>
                    </a>
                </div>
            </header>

            <!-- Main content area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <!-- Form tambah jenis kue -->
                <div class="mt-8 max-w-md mx-auto">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Tambah Jenis Kue</h3>
                        <form action="dasbordAdmin.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="action" value="create">
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kue:</label>
                                <input type="text" name="nama"
                                    class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Kue:</label>
                                <input type="file" name="gambar" accept="image/*"
                                    class="mt-1 block w-full text-gray-300" required>
                            </div>
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Error and success messages -->
                <?php if ($error_message): ?>
                    <div id="success_message" class="absolute top-0 left-0 right-0 mx-auto w-full bg-green-500 text-white p-4 rounded mb-4 max-w-4xl"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div id="success_message" class="bg-green-500 text-white p-4 rounded mb-4">
                        <?php echo $success_message; ?>
                    </div>
                    <script>
                        setTimeout(function () {
                            document.getElementById('success_message').style.display = 'none';
                        }, 3000);
                    </script>
                <?php endif; ?>

                <!-- Grid Jenis Kue -->
                <div class="mt-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Jenis Kue</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($jenisKue = mysqli_fetch_assoc($result)): ?>
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <img src="<?php echo $jenisKue['gambar']; ?>"
                                    alt="Gambar Kue" class="w-full h-40 object-cover rounded-t-lg">
                                <div class="mt-4">
                                    <h4 class="text-lg font-semibold text-gray-800"><?php echo $jenisKue['nama']; ?></h4>
                                    <div class="flex justify-between items-center mt-4">
                                        <form action="dasbordAdmin.php" method="POST"
                                            onsubmit="return confirm('Anda yakin ingin menghapus jenis kue ini?');">
                                            <input type="hidden" name="id_jenis"
                                                value="<?php echo $jenisKue['id_jenis']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit"
                                                class="text-white bg-red-500 hover:bg-red-700 focus:outline-none px-4 py-2 rounded">Hapus</button>
                                        </form>
                                        <button onclick="toggleUpdateForm(<?php echo $jenisKue['id_jenis']; ?>)"
                                            class="text-white bg-blue-500 hover:bg-blue-700 focus:outline-none px-4 py-2 rounded">Edit</button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>

                    </div>
                </div>

                <!-- Form update jenis kue (pop-up/modal) -->
                <div id="updateFormContainer"
                    class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden justify-center items-center">
                    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
                        <h3 class="text-xl font-semibold text-gray-700 mb-4">Update Jenis Kue</h3>
                        <form id="updateForm" action="dasbordAdmin.php" method="POST" enctype="multipart/form-data"
                            class="space-y-4">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id_jenis" id="update_id_jenis">
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Kue:</label>
                                <input type="text" name="nama" id="update_nama"
                                    class="mt-1 p-2 block w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label for="gambar" class="block text-sm font-medium text-gray-700">Gambar Kue:</label>
                                <input type="file" name="gambar" id="update_gambar" accept="image/*"
                                    class="mt-1 block w-full text-gray-300">
                            </div>
                            <div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">Update</button>
                            </div>
                        </form>
                        <button onclick="closeUpdateForm()"
                            class="mt-4 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">Cancel</button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleUpdateForm(id_jenis) {
            document.getElementById('updateFormContainer').classList.remove('hidden');
            document.getElementById('update_id_jenis').value = id_jenis;
            document.getElementById('update_nama').value = name;
        }

        function closeUpdateForm() {
            document.getElementById('updateFormContainer').classList.add('hidden');
        }
    </script>
</body>

</html>