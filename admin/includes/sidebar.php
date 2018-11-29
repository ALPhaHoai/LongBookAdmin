<?php
/**
 * Created by Long
 * Date: 11/19/2018
 * Time: 1:50 PM
 */
?>
<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
<div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav side-nav">
        <li style="background:#1b926c;color:#fff;">
            <a href="index.php" style="color:#fff;"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#demo_dm">
                <i class="fa fa-fw fa-file"></i> Truyện <i class="fa fa-fw fa-caret-down"></i>
            </a>
            <ul id="demo_dm" >
                <li>
                    <a href="list_book.php">Danh sách</a>
                </li>
                <li>
                    <a href="search_book.php">Tìm kiếm</a>
                </li>
                <li>
                    <a href="add_book.php">Thêm mới</a>
                </li>

            </ul>
        </li>
       <!-- <li>
            <a href="javascript:;" data-toggle="collapse" data-target="#demo_bv"><i
                        class="fa fa-fw fa-file"></i> Thể loại <i class="fa fa-fw fa-caret-down"></i></a>
            <ul id="demo_bv" class="collapse">
                <li>
                    <a href="#">Danh sách</a>
                </li>
                <li>
                    <a href="#">Tìm kiếm</a>
                </li>
                <li>
                    <a href="#">Thêm mới</a>
                </li>


            </ul>
        </li>-->

    </ul>
</div>
<!-- /.navbar-collapse -->
