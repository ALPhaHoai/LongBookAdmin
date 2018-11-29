var API_ENDPOINT = "http://localhost:8080/longbookapi/";
var API_ENDPOINT_BOOK = API_ENDPOINT + "book";
var API_ENDPOINT_BOOK_SEARCH = API_ENDPOINT_BOOK + "/search";
// var API_ENDPOINT_CATEGORY = API_ENDPOINT + "category";

//List and Search book
var start = 0;
var limit = 10;
var moreResult = true;
var pending = false;

function remove(id) {
    if (confirm('Are you sure?')) {
        $.ajax({
            url: API_ENDPOINT_BOOK + '/' + id,
            type: 'DELETE',
            crossDomain: true,
            crossOrigin: true,
            dataType: 'json',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic YWRtaW46YWRtaW4='
            },
            complete: function (response) {
                if (response.status === 200) {
                    $('#' + id).remove();
                    console.log(response.responseJSON.message);
                    alert("Xóa thành công");
                } else {
                    if (response.status === 0) {
                        alert("Không thể kết nối tới server");
                    }else if (response.hasOwnProperty("responseJSON")) {
                        alert(response.responseJSON.message);
                    } else {
                        alert("Đã có lỗi xảy ra");
                    }

                }
            }
        });
    }
}

function buildResult(data) {
    if ((typeof data).toLowerCase() !== "object" || data.length === 0) return;
    $('#search-result').show();
    for (var i = 0; i < data.length; i++) {
        var id = data[i].id;
        var title = data[i].title;
        var content = data[i].content;
        if (content.length > 500) content = content.substr(0, 500) + "...";
        var row =
            "<tr id=\"" + id + "\">" +
            "<td>" + id + "</td>" +
            "<td>" + title + "</td>" +
            "<td>" + content + "</td>" +
            "<td><a href=\"./edit_book.php?id=" + id + "\"><i class=\"fa fa-edit\"></i></a></td>" +
            "<td><a href=\"javascript:remove(" + id + ")\"><i class=\"fa fa-remove\"></i></a></td>" +
            "</tr>";
        $('#search-result tbody').append(row);
    }
}

function doSearch(startInput = 0) {
    start = startInput;
    var title = $('#book_title').val().trim();
    var content = $('#book_content').val().trim();
    var categories = $('#sel').selectpicker().val();

    if (title === "" && content === "" && categories == null) {
        alert('Bạn phải nhập thông tin tìm kiếm');
        return;
    }

    var query = "?start=" + startInput + "&limit=" + limit;
    if (title !== "") query += "&title=" + encodeURI(title);
    if (content !== "") query += "&content=" + encodeURI(content);
    if (categories != null) query += "&categories=" + encodeURI(categories.toString());

    pending = true;
    $.ajax({
        url: API_ENDPOINT_BOOK_SEARCH + query,
        type: 'GET',
        crossDomain: true,
        crossOrigin: true,
        dataType: 'json',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Basic YWRtaW46YWRtaW4='
        },
        complete: function (response) {
            if (startInput === 0) $('#search-result tbody tr').remove();
            if (response.status === 200) {
                moreResult = true;
                console.log(response.responseJSON.result);
                buildResult(response.responseJSON.result);
                start = startInput + limit;
            } else if (response.status === 0) {
                alert("Không thể kết nối tới server");
            } else {
                moreResult = false;
                if (startInput === 0) {
                    $('#search-result').hide();
                    if (response.hasOwnProperty("responseJSON")) {
                        alert(response.responseJSON.message);
                    } else {
                        alert("Đã có lỗi xảy ra");
                    }
                }
            }
            pending = false;
        }
    });
}

function getBooks(startInput = 0) {
    start = startInput;
    pending = true;
    $.ajax({
        url: API_ENDPOINT_BOOK + "?start=" + startInput + "&limit=" + limit,
        type: 'GET',
        crossDomain: true,
        crossOrigin: true,
        dataType: 'json',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Basic YWRtaW46YWRtaW4='
        },
        complete: function (response) {
            if (response.status === 200) {
                moreResult = true;
                console.log(response.responseJSON.result);
                buildResult(response.responseJSON.result);
                start = startInput + limit;
            } else if (response.status === 0) {
                alert("Không thể kết nối tới server");
            } else {
                moreResult = false;
                if (startInput === 0) {
                    if (response.hasOwnProperty("responseJSON")) {
                        alert(response.responseJSON.message);
                    } else {
                        alert("Đã có lỗi xảy ra");
                    }
                }
            }
            pending = false;
        }
    });
}


//Edit book
//Thêm 1 thể loại mới vào danh sách các thể loại có sẵn của truyện
function updatenewCatetoOldValue(new_catetegories) {
    if (new_catetegories == null) $('#sel').attr('old_value', "");
    else {
        var old_categories = $('#sel').attr('old_value').replace("[", "").replace("]", "").split(",");
        var new_catetegories_id = [];
        for (let i = 0; i < new_catetegories.length; i++) {
            new_catetegories_id.push(new_catetegories[i].id);
        }
        $('#sel').attr('old_value', new_catetegories_id.toString());
    }
}

//Kiểm tra 2 mảng có bằng nhau không (các giá trị bằng nhau)
function arraysEqual(arr1, arr2) {
    if (arr1.length === 0 && arr2.length === 0) return true;
    if (arr1.length !== arr2.length) return false;
    for (var i = arr1.length; i--;) {
        if (arr1[i] !== arr2[i]) return false;
    }
    return true;
}

//Trả về 1 mảng có giá trị xuất hiện ở arr1 mà không xuất hiện ở arr2
function subArrays(arr1, arr2) {
    if (arr1.length === 0 || arr2.length === 0) return arr1;
    var sub_arr = [];
    for (var i = 0; i < arr1.length; i++) {
        if (!arr2.includes(arr1[i])) sub_arr.push(arr1[i]);
    }
    return sub_arr;
}

function edit(bookId) {
    let old_categories_str = $('#sel').attr('old_value').replace("[", "").replace("]", "");
    var old_categories = (old_categories_str === "") ? [] : old_categories_str.split(",");
    var categories = $('#sel').selectpicker().val() == null ? [] : $('#sel').selectpicker().val();

    var book = {
        title: $('#book_title').val().trim(),
        content: $('#book_content').val().trim()
    };

    var inputValid = isValidBook(book);
    if("" !== inputValid) {
        alert(inputValid);
        return ;
    }
    var is_update_content = (book.title !== $('#book_title').attr('old_value') || book.content !== $('#book_content').attr('old_value'));
    var is_update_cate = (!arraysEqual(old_categories, categories));

    if (!is_update_content && !is_update_cate) {
        alert('Bạn chưa sửa gì');
        return;
    }

    // console.log(data);
    var count_ajax = 0;
    if (is_update_content) count_ajax++;
    if (is_update_cate) count_ajax++;
    var error = false;
    var error_message = "";

    //update book
    if (is_update_content) {
        $.ajax({
            url: API_ENDPOINT_BOOK + "/" + bookId,
            type: 'PUT',
            data: JSON.stringify(book),
            crossDomain: true,
            crossOrigin: true,
            async: false,
            dataType: 'json',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic YWRtaW46YWRtaW4='
            },
            complete: function (response) {
                if (response.status === 200) {
                    $('#book_title').val(response.responseJSON.result.title);
                    $('#book_title').attr('old_value', response.responseJSON.result.title);
                    $('#book_content').val(response.responseJSON.result.content);
                    $('#book_content').attr('old_value', response.responseJSON.result.content);
                    console.log(response.responseJSON.result);
                } else {
                    error = true;
                    if (response.status === 0) {
                        error_message += "Không thể kết nối tới server\n";
                    } else if (response.hasOwnProperty("responseJSON")) {
                        error_message += response.responseJSON.message + "\n";
                    } else {
                        error_message += "Đã có lỗi xảy ra\n";
                    }
                }
                if (--count_ajax < 1) {
                    if (error) alert(error_message);
                    else alert("Sửa thành công");
                }
            }
        });
    }
    //update book category
    if (is_update_cate) {
        let cate_data = "";
        for (let i = 0; i < categories.length; i++) {
            cate_data += "{\"id\":" + categories[i] + "}";
            if (i < categories.length - 1) cate_data += ",";
        }
        if (cate_data !== "") cate_data = "[" + cate_data + "]";
        $.ajax({
            url: API_ENDPOINT_BOOK + "/" + bookId + "/category",
            type: 'PUT',
            data: (cate_data),
            crossDomain: true,
            crossOrigin: true,
            async: false,
            dataType: 'json',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic YWRtaW46YWRtaW4='
            },
            complete: function (response) {
                if (response.status === 200) {
                    updatenewCatetoOldValue(response.responseJSON.result);
                } else {
                    error = true;
                    if (response.status === 0) {
                        error_message += "Không thể kết nối tới server\n";
                    }
                    else if (response.hasOwnProperty("responseJSON")) {
                        error_message += response.responseJSON.message + "\n";
                    } else {
                        error_message += "Đã có lỗi xảy ra\n";
                    }
                }
                if (--count_ajax < 1) {
                    if (error) alert(error_message);
                    else alert("Sửa thành công");
                }
            }
        });

    }
}


//Insert new book
function add() {
    var data = {
        title: $('#book_title').val().trim(),
        content: $('#book_content').val().trim()
    };

    let inputValid = isValidBook(data);
    if("" !== inputValid) {
        alert(inputValid);
        return ;
    }

    // console.log(data);

    $.ajax({
        url: API_ENDPOINT_BOOK,
        type: 'POST',
        data: JSON.stringify(data),
        crossDomain: true,
        crossOrigin: true,
        dataType: 'json',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Basic YWRtaW46YWRtaW4='
        },
        complete: function (response) {
            if (response.status === 200) {
                console.log(response.responseJSON.result);
                var book = response.responseJSON.result;
                //update book category
                var categories = $('#sel').selectpicker().val();
                if (categories != null && categories.length > 0) {
                    let cate_data = "";
                    for (let i = 0; i < categories.length; i++) {
                        cate_data += "{\"id\":" + categories[i] + "}";
                        if (i < categories.length - 1) cate_data += ",";
                    }
                    if (cate_data !== "") {
                        $.ajax({
                            url: API_ENDPOINT_BOOK + "/" + book.id + "/category",
                            type: 'PUT',
                            data: "[" + cate_data + "]",
                            crossDomain: true,
                            crossOrigin: true,
                            async: false,
                            dataType: 'json',
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Basic YWRtaW46YWRtaW4='
                            },
                            complete: function (response) {
                                if (response.status === 200) {
                                }
                                else if (response.status === 0) {
                                    error_message += "Không thể kết nối tới server\n";
                                }
                                else if (response.hasOwnProperty("responseJSON")) {
                                    error_message += response.responseJSON.message + "\n";
                                } else {
                                    error_message += "Đã có lỗi xảy ra\n";
                                }
                            }
                        });
                    }

                }

                window.location = "edit_book.php?id=" + book.id + "&success_message=" + encodeURI("Thêm thành công truyện \"" + book.title + "\"");

            } else if (response.status === 0) {
                alert("Không thể kết nối tới server");
            } else if (response.hasOwnProperty("responseJSON")) {
                alert(response.responseJSON.message);
            } else {
                alert("Đã có lỗi xảy ra");
            }
        }
    });
}
function isValidBook(book) {
    let msg = "";
    if (book.title == null || book.title.trim() === "") {
        msg += '\nBạn phải nhập tiêu đề';
    }
    else if (book.title.length < 3) {
        msg += '\nTiêu đề quá ngắn';
    }

    if (book.content == null || book.content.trim() === "") {
        msg += '\nBạn phải nhập nội dung';
    }
    else if (book.content.length < 3) {
        msg += '\nNội dung quá ngắn';
    }
    return msg.trim();
}