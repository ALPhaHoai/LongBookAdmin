<?php
require_once __DIR__ . "/lib/my_curl.php";
require_once __DIR__ . "/api_config.php";
//all category in database
$categories = json_decode(my_curl(["url" => $API_ENDPOINT_CATEGORY . "?limit=100"])["content"]);
if ($categories != null && $categories->status === 200) $categories = $categories->result;
else $categories = null;

?><!DOCTYPE html>
<html lang="en">

<head>
    <title>Tìm kiếm truyện</title>
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
                            <i class="fa fa-search"></i> Tìm kiếm
                        </li>
                    </ol>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="form_search_book">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" id="book_title" class="form-control"
                                   placeholder="Tiêu đề truyện">
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea type="text" name="content" id="book_content" class="form-control" rows="8"
                                      placeholder="Nội dung truyện"></textarea>
                        </div>
                        <?php
                        if (is_array($categories) && count($categories) > 0) {
                            ?>
                            <div class="form-group">
                                <label for="sel">Thể loại</label>
                                <select id="sel" class="form-control selectpicker" data-selected-text-format="count > 6"
                                        multiple title="Các thể loại">
                                    <?php
                                    foreach ($categories as $cate) {
                                        echo "<option class=\"category\" value=\"" . $cate->id . "\">" . $cate->name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                        <input type="submit" name="submit" class="btn btn-primary" value="Tìm kiếm">
                    </form>
                </div>
                <script>
                    $(document).ready(function () {
                        //Search
                        $('#form_search_book').on('submit', function (e) {
                            e.preventDefault();
                            doSearch(0);
                        });

                        $(window).scroll(function () {
                            if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                                if (!$('#search-result').is(":hidden") && moreResult && !pending)
                                    doSearch(start);
                            }
                        });
                    });
                </script>
            </div>
            <div id="search-result" style="display: none">
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
                            </tbody>
                        </table>

                    </div>
                </div>
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


