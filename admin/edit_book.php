<?php
/**
 * Created by Long
 * Date: 11/19/2018
 * Time: 3:37 PM
 */
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) return;

require_once __DIR__ . "/lib/my_curl.php";
require_once __DIR__ . "/api_config.php";

$book = json_decode(my_curl(["url" => $API_ENDPOINT_BOOK . "/" . $_GET["id"]])["content"]);

if ($book != null) {
//all category of this book
    $book_categories = json_decode(my_curl(["url" => $API_ENDPOINT_BOOK . "/" . $_GET["id"] . "/category"])["content"]);
    if ($book_categories != null && $book_categories->status === 200) $book_categories = $book_categories->result;
    else $book_categories = null;

    $book_categories_id = array();
    if (is_array($book_categories)) foreach ($book_categories as $book_category) {
        $book_categories_id[] = $book_category->id;
    }

//all category in database
    $categories = json_decode(my_curl(["url" => $API_ENDPOINT_CATEGORY . "?limit=100"])["content"]);
    if ($categories != null && $categories->status === 200) $categories = $categories->result;
    else $categories = null;

    $category_select = array();
    if (is_array($categories)) foreach ($categories as $category) {
        //check if cate is in array (in_array function is not working)
        $in_array = false;
        if (is_array($book_categories)) foreach ($book_categories as $item) {
            if ($item->id == $category->id) {
                $in_array = true;
                break;
            }
        }
        $category->selected = $in_array;
        $category_select[] = $category;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sửa truyện</title>
    <?php
    include __DIR__ . "/includes/head.php";
    ?>
</head>

<body>

<div id="wrapper">
    <?php
    include __DIR__ . "/includes/header.php";
    if(isset($_GET['success_message'])) {
        ?>
        <script>
            window.history.pushState({"pageTitle": document.title}, "", window.location.href.substr(0, window.location.href.indexOf("&success_message=")));
            $(document).ready(function (){alert('<?=$_GET['success_message']?>')});
        </script>
        <?php
    }
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
                            <i class="fa fa-edit"></i> Sửa
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php
                    if ($book == null || !isset($book->status)) {
                        echo "<div class = 'alert alert-danger'>Không thể kết nối tới server</div>";
                    } else if ($book->status !== 200) {
                        echo "<div class = 'alert alert-danger'>$book->message</div>";
                    } else {
                        ?>
                        <form id="form_edit_book">
                            <div class="form-group">
                                <label>Id</label>
                                <input type="text" disabled="disabled" name="id" id="book_id" class="form-control"
                                       value="<?= $book->result->id ?>">
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" id="book_title" class="form-control"
                                       placeholder="Tiêu đề truyện" old_value="<?= $book->result->title ?>"
                                       value="<?= $book->result->title ?>">
                            </div>
                            <div class="form-group">
                                <label>Content</label>
                                <textarea name="content" id="book_content" class="form-control"
                                          placeholder="Nội dung truyện" rows="8"
                                          old_value="<?= $book->result->content ?>"><?= $book->result->content ?></textarea>
                            </div>
                            <?php
                            if (is_array($category_select) && count($category_select) > 0) {
                                ?>
                                <div class="form-group">
                                    <label for="sel">Thể loại</label>
                                    <select id="sel" old_value="<?php echo json_encode($book_categories_id) ?>"
                                            class="form-control selectpicker" data-selected-text-format="count > 6"
                                            multiple
                                            title="Các thể loại">
                                        <?php
                                        foreach ($category_select as $cate) {
                                            echo "<option class=\"category\" value=\"" . $cate->id . "\"";
                                            if ($cate->selected) echo " selected";
                                            echo ">" . $cate->name . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php } ?>
                            <input type="submit" name="submit" class="btn btn-primary" value="Sửa">
                        </form>

                        <?php
                    }
                    ?>
                </div>
                <script>
                    var bookId = <?= $book->result->id ?>;
                    $(document).ready(function () {
                        //Add new book
                        $('#form_edit_book').on('submit', function (e) {
                            e.preventDefault();
                            edit(bookId);
                        });

                    });
                </script>
            </div>


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


