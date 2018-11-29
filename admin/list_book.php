<?php
/**
 * Created by Long
 * Date: 11/19/2018
 * Time: 4:07 PM
 */
require_once __DIR__ . "/lib/my_curl.php";
require_once __DIR__ . "/api_config.php";

$html = my_curl(["url" => $API_ENDPOINT_BOOK])["content"];
$book = json_decode($html);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Danh sách truyện</title>
    <?php
    include __DIR__ . "/includes/head.php";
    ?>
</head>

<body>

<div id="wrapper">
    <?php
    include __DIR__ . "/includes/header.php";
    ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Truyện
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-list"></i> Danh sách
                        </li>
                    </ol>
                </div>
            </div>
            <?php
            if ($book == null || !isset($book->status)) {
                echo "<div class = 'alert alert-danger'>Không thể kết nối tới server</div>";
            } else if ($book->status != 200) {
                echo "<div class = 'alert alert-danger'>$book->message</div>";
            } else {
                ?>
                <div id="search-result">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Tiêu đề</th>
                                    <th>Nội dung</th>
                                    <th>Sửa</th>
                                    <th>Xóa</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                foreach ($book->result as $item) {
                                    ?>
                                    <tr id="<?= $item->id ?>">
                                        <td><?= $item->id ?></td>
                                        <td><?php echo htmlspecialchars($item->title) ?></td>
                                        <td><?php $content = htmlspecialchars($item->content);
                                            if (strlen($content) > 500) $content = substr($content, 0, 500);
                                            echo $content . "..."; ?></td>
                                        <td><a href="./edit_book.php?id=<?= $item->id ?>"><i class="fa fa-edit"></i></a>
                                        </td>
                                        <td><a href="javascript:remove(<?= $item->id ?>)"><i
                                                        class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function () {
                            $(window).scroll(function () {
                                if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                                    if (moreResult && !pending)
                                        getBooks(start);
                                }
                            });
                        });
                    </script>
                </div>
                <?php
            }
            ?>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->

    </div>
    <?php
    include __DIR__ . "/includes/footer.php";
    ?>
</div>

</body>

</html>



