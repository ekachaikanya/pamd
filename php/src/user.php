<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekachaik Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <?php 
        // สร้างไฟล์ nav.php ไว้ด้วยนะครับ หรือถ้าไม่มีให้ลบบรรทัดนี้ออก
        if (file_exists('nav.php')) { require_once 'nav.php'; } 
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                
                <div class="card p-4 mb-4">
                    <h2 class="mb-3 text-primary">📦 รายชื่อผู้ใช้งาน (User List)</h2>

                    <?php
                        // ดึงค่า Config มาจาก Environment Variable ของ Docker
                        $host = getenv('MYSQL_HOST') ?: 'db';
                        $user = getenv('MYSQL_USER') ?: 'MYSQL_USER';
                        $pass = getenv('MYSQL_PASSWORD') ?: 'MYSQL_PASSWORD';
                        $db   = getenv('MYSQL_DATABASE') ?: 'MYSQL_DATABASE';

                        // ปิดการแจ้งเตือน Error หน้าเว็บ (เพื่อความสวยงามและปลอดภัย)
                        mysqli_report(MYSQLI_REPORT_OFF);

                        $conn = new mysqli($host, $user, $pass, $db);

                        // เช็คการเชื่อมต่อ
                        if ($conn->connect_error) {
                            // ถ้าพัง ให้แสดง Alert สีแดง
                            echo '<div class="alert alert-danger" role="alert">';
                            echo '❌ <strong>Connection Failed:</strong> ' . $conn->connect_error;
                            echo '</div>';
                            // หยุดการทำงานส่วนล่างถ้าต่อ DB ไม่ได้
                            $conn->close();
                            exit(); 
                        } else {
                            // ถ้าสำเร็จ แสดง Alert สีเขียว (กดปิดได้)
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
                            echo '✅ Connected to MySQL server successfully!';
                            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            echo '</div>';
                        }

                        // ดึงข้อมูล
                        $users = [];
                        $sql = "SELECT * FROM users";
                        // สร้างตาราง users จำลอง ถ้ายังไม่มี (เฉพาะตัวอย่างนี้)
                        $conn->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, first_name VARCHAR(50), last_name VARCHAR(50), age INT)");
                        // ใส่ข้อมูลจำลอง ถ้าตารางว่าง (เฉพาะตัวอย่างนี้)
                        $check = $conn->query("SELECT count(*) as count FROM users")->fetch_object();
                        if ($check->count == 0) {
                            $conn->query("INSERT INTO users (first_name, last_name, age) VALUES ('Somchai', 'Jaidee', 30), ('Somsri', 'Rakrian', 25)");
                        }

                        if ($result = $conn->query($sql)) {
                            while($data = $result->fetch_object()) {
                                $users[] = $data;
                            }
                        }
                    ?>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Age</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach($users as $index => $u): ?>
                                    <tr>
                                        <th scope="row"><?= $index + 1 ?></th>
                                        <td><?= htmlspecialchars($u->first_name) ?></td>
                                        <td><?= htmlspecialchars($u->last_name) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($u->age) ?> ปี</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">ไม่พบข้อมูลผู้ใช้งาน</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div> </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>