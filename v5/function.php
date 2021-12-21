<?php
// koneksi
$con = mysqli_connect('localhost', 'root', '', 'savelinks');
if( !$con ){
    die("Koneksi Gagal");
}

$error = "";

// --- Main Query ---
function query($query) {
global $con;
    $result = mysqli_query($con, $query) or die('Gagal menampilkan data');;
    $rows = [];
    while($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}


/* --- USER QUERY --- */
// --- Register ---
// function register($data) { //menangkap value $_POST dengan variabel $data
// global $con;
//     // VARIABLE 1 - membersihkan blackslash dan mengubah menjadi huruf kecil semua
//     $username = strtolower(stripslashes($data["username"]));
//     // VARIABLE 2 - memungkinkan user memasukan password dengan tanda kutip
//     $password = mysqli_real_escape_string($con, $data["password"]);
//     $confirmPass = mysqli_real_escape_string($con, $data["confirm_password"]);
//     $email = mysqli_real_escape_string($con, $data["email"]);
//     // VARIABLE 2 - mefilter html
//     $username = htmlspecialchars($username);
//     $password = htmlspecialchars($password);
//     $confirmPass = htmlspecialchars($confirmPass);
//     $email = htmlspecialchars($email);

//     // KONDISI 1 - Cek username sudah terpakai atau belum
//     $result = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
//     if(mysqli_fetch_assoc($result)) { // mengeluarkan data dan memberi kondisi 1
//         $_POST['error'] = "username tidak tersedia!";
//         return false; // mengembalikan nilai false kepada else di register php
//     }

//     // KONDISI 2 - Jika user tersedia maka cek email
//     $result = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
//     if(mysqli_fetch_assoc($result)) {
//         $_POST['error'] = "email sudah pernah didaftarkan";
//         return false; // mengembalikan nilai false kepada else di register php
//     }

//     // KONDISI 3 - Jika KONDISI 2 berhasil maka Lakukan cek konfirmasi password
//     if($password !== $confirmPass) {
//         $_POST['errorPass'] = "Konfirmasi Password tidak sesuai";
//         return false;
//     }

//     // Jika Kondisi 1 & 2 & 3 berhasil maka 
//     // Enkripsi password lalu menambahkan user
//     $password = password_hash($password, PASSWORD_DEFAULT);
//     mysqli_query($con, "INSERT INTO users SET username='$username', password='$password', email='$email' ");

//     // Jika semua query berhasil mengembalikan nilai true
//     $_POST['succes'] = "Registrasi berhasil!";
//     return mysqli_affected_rows($con);
// }



// --- Login ---
function login($data){
global $con;

    // VARIABEL 1
    $username = $data['username'];
    $password = $data['password'];

        // QUERY 1 - Mengecek kesamaan username di database dgn input beserta password
        $query = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($con, $query);

        // KONDISI 1 - Jika username dan password ada di database
        if(mysqli_num_rows($result) === 1 ) {
            // KONDISI 2 - Mengecek Password
            $row = mysqli_fetch_assoc($result);
            if(password_verify($password, $row['password'])){
                // SESSION - Membuat 1 session dengan key sesiLogin
                $_SESSION['sesiLogin'] = $username;
                $_SESSION['sesiId'] = $row['id'];

                // COOKIE - Membuat fitur remember me
                if(isset($data['remember'])) {
                    // Memasukan cookie ke database
                    $id = $row['id'];
                    $key = hash('sha256', $row['username']); //variabel $key dengan isi username yang diacak
                    mysqli_query($con, "UPDATE users SET cookie='$key' WHERE id=$id");
                    setcookie('id', $id, time()+7*24*60*60); // mengirim id ke key id // set cookie selama 7 hari
                }

                return true;
            } else {
                $_POST['error'] = "Invalid Username Or Password";
                return false;
            }
        } else {
            $_POST['error'] = "Invalid Username Or Password";
            return false;
        }
    //} else {
    //    return false;
    //}
}



// --- Update Profile ---
function updateProfile($data){
global $con;
    // Mengambil data menjadi variabel
    $id = $data["id"];
    $name = htmlspecialchars($data['name']);
    $username = htmlspecialchars($data['username']);
    $email = htmlspecialchars($data['email']);
    $passwordLama = ($data['password']);


    // Jika user mengganti password
    if($_POST['new-pass'] == ""){ // mengecek apakah key new-pass kosong atau tidak
        $password = $passwordLama; // jika kosong maka variabel password diisi dngn password lama
    } else {
        $password = newPass(); // jika ada isinya maka variabel password diisi dngn function newPass
    }

    // query insert data
    $query = "UPDATE users SET name='$name', username='$username', password='$password', email='$email' WHERE id=$id ";
    if(!mysqli_query($con, $query)){
        return false;
    }
    $_SESSION['sesiLogin'] = $username;
    return mysqli_affected_rows($con);
}

// --- NEW PASS ---
function newPass(){
    // variabel
    $newPass = $_POST['new-pass'];
    $confirmNewPass = $_POST['confirm-new-pass'];
    $passwordLama = $_POST["password"];
    // var_dump($passwordLama);die;

    // mengecek kesamaan password
    if($newPass !== $confirmNewPass) {
        echo "<script>
        alert('Invalid Confirm Pass');
        </script>";
        return $passwordLama; // jika pasword salah maka diisi dengan password lama
    }

    // enkripsi password
    $newPass = password_hash($newPass, PASSWORD_DEFAULT);
    return $newPass; // jika berhasil maka mengembalikan nilai new password
}



// --- Add Link ---
function addlink($data) {
global $con;

    // VARIABEL 1
    $linkTitle = $data['link-title'];
    $linkUrl = $data['link-url'];
    $idUser = $_SESSION['sesiId'];

    // KONDISI - Mengecek apakah form kosong
    if($linkTitle != "" && $linkUrl != "") {
        // insert data
        $query = "INSERT INTO links SET id_user=$idUser, title='$linkTitle', url='$linkUrl' ";
        $result = mysqli_query($con, $query);
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "!!!!";
        return false;
    }
}


// --- Update Link ---
function updateLink($data) {
global $con;
    $linkTitle = $data['link-title'];
    $linkUrl = $data['link-url'];
    $idUser = $_SESSION['sesiId'];
    $idLink = $data['id-link'];

    // KONDISI - Mengecek apakah form kosong
    if($linkTitle != "" && $linkUrl != "") {
        // Maka jalankan query update data
        $query = "UPDATE links SET title='$linkTitle', url='$linkUrl' WHERE id_link=$idLink";
        $result = mysqli_query($con, $query);
        return mysqli_affected_rows($con);
    } else {
        $_POST['error'] = "!!!!";
        return false;
    }
}



// --- Search ---
function search($keyword){
global $con;
$idUser = $_SESSION['sesiId'];
    $query = "SELECT * FROM links WHERE id_user=$idUser and title LIKE '%$keyword%'";

    return query($query);
}







/* --- ADMIN PAGE --- */
// --- Update ---
function update($data){
    global $con;
    // membuat variabel
    // mencegah adanya element html
    $id = $data["id"];
    $username = htmlspecialchars($data['username']);
    $password = htmlspecialchars($data['password']);
    $password = password_hash($password, PASSWORD_DEFAULT); // enkripsi password
    $email = htmlspecialchars($data['email']);

    // query insert data
    $query = "UPDATE users SET username='$username', password='$password', email='$email' WHERE id=$id ";
    mysqli_query($con, $query);
    // mengembalikan nilai angka dari query
    // kalo berhasil maka '1' klo gagal maka '-1'
    return mysqli_affected_rows($con);
}

?>