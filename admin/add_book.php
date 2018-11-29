<?php
/**
 * Created by Long
 * Date: 11/19/2018
 * Time: 2:19 PM
 */

require_once __DIR__ . "/lib/my_curl.php";
require_once __DIR__ . "/api_config.php";

//all category in database
$categories = json_decode(my_curl(["url" => $API_ENDPOINT_CATEGORY . "?limit=100"])["content"]);
if ($categories != null && $categories->status === 200) $categories = $categories->result;
else $categories = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Thêm mới truyện</title>
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
                            <i class="fa fa-plus"></i> Thêm mới
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form action="<?= $API_ENDPOINT_BOOK ?>" method="POST" id="form_add_book">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" id="book_title" class="form-control"
                                   placeholder="Tiêu đề truyện">
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea type="text" name="content" id="book_content" class="form-control"
                                      rows="8" placeholder="Nội dung truyện"></textarea>
                        </div>
                        <?php
                        if (is_array($categories) && count($categories) > 0) {
                            ?>
                            <div class="form-group">
                                <label for="sel">Thể loại</label>
                                <select id="sel" class="form-control selectpicker" data-selected-text-format="count > 6"
                                        multiple
                                        title="Các thể loại">
                                    <?php
                                    foreach ($categories as $cate) {
                                        echo "<option class=\"category\" value=\"" . $cate->id . "\">" . $cate->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                        <input type="submit" name="submit" class="btn btn-primary" value="Thêm mới">
                    </form>
                </div>
                <script>
                    $(document).ready(function () {
                        //Add new book
                        $('#form_add_book').on('submit', function (e) {
                            e.preventDefault();
                            add();
                        });
                    });
                </script>
            </div>
        </div>
        <!-- /.container-fluid -->

    </div>
    <?php
    include __DIR__ . "/includes/footer.php";
    ?>
</div>

</body>

</html>


