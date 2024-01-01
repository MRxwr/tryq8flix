$(document).ready(function(){
	$('#threeDots, #playVideo, #history, #disLikedVidoesModal, #likedVidoesModal').on('hidden.bs.modal', function (e) {
		$(this).find('.modal-body').empty(); // clear the body of the modal
	});
});

$(document).on('click', '.playVideo', function(e) {
	$("#loading-screen").show();
    var getId = $(this).attr("id");
    $.post("requests/index.php?type=playVideo", {
            id: getId
        },
        function(data) {
            $("#loading-screen").hide();
            $("#videoPlayer").html(data);
            $("#videoPlayerTitle").html(title);
        }
    );
});

$(document).on('click', '.threeDots', function(e) {
    var getId = $(this).attr("id");
    //var title = $(".categoryTitle" + getId).html();
    $.post("requests/index.php?type=more", {
		id: getId
	},
	function(data) {
		$("#moreBody").html(data);
		$("#moreTitle").html();
	});
});

$(document).on('click', '.threeDots2', function(e) {
    var getId = $(this).attr("id");
    //var title = $(".categoryTitle" + getId).html();
    $.post("requests/index.php?type=more", {
		id: getId
	},
	function(data) {
		$("#moreBody2").html(data);
		$("#moreTitle2").html();
	});
});

$(document).on('click', '#regBtn', function(e) {
    var username = $("input[name=regUser]").val();
    var pass = $("input[name=regPass]").val();
    var rePass = $("input[name=regPass1]").val();
    var email = $("input[name=regEmail]").val();
    $.post("requests/index.php?type=register", {
            username: username,
            pass: pass,
            rePass: rePass,
            email: email
        },
        function(data) {
            if (data == 0) {
                alert("You have been registred successfully, Please login now.");
                $('#signup').modal('hide');
                $('#login').modal('show');
				$("input[name=regUser]").val("");
				$("input[name=regPass]").val("");
				$("input[name=regPass1]").val("");
				$("input[name=regEmail]").val("");
            } else {
                alert(data);
            }
        });
});

$(document).on('click', '#loginBtn', function(e) {
    var username = $("input[name=loginUser]").val();
    var pass = $("input[name=loginPass]").val();
    $.post("requests/index.php?type=login", {
            username: username,
            pass: pass
        },
        function(data) {
            if (data == 0) {
                alert("You have been logged in successfully");
                location.reload(true);
            } else {
                alert(data);
            }
        });
});

function bodyLoad() {
    if ($.cookie('tryq8flix2')) {
        $("#profileOptions0").attr("style", "display:none");
        $("#profileOptions1").attr("style", "display:block");
		$(".bi-gear-fill").attr("style","color:black");
		$(".bi-film").attr("style","color:black");
		$(".bi-tv").attr("style","color:black");
		$(".bi-house-fill").attr("style","color:#9f8d5c");
		$(".bi-search").attr("style","color:black");
		$.post("requests/index.php?type=home", {
			type: "get",
		},
		function(data) {
			$("#loading-screen").hide();
			$("#mainBody").html("");
			$("#mainBody").append(data);
		});
    } else {
		$("#loading-screen").hide();
		$(".bi-gear-fill").attr("style","color:black");
		$(".bi-film").attr("style","color:black");
		$(".bi-tv").attr("style","color:black");
		$(".bi-house-fill").attr("style","color:#9f8d5c");
		$(".bi-search").attr("style","color:black");
        $("#profileOptions0").attr("style", "display:block");
        $("#profileOptions1").attr("style", "display:none");
    }
}

$(document).on('click', '#logoutLabel', function(e) {
    $.post("requests/index.php?type=logout", {
            type: "remove",
        },
        function(data) {
            if (data == 0) {
                alert("You have been logged out successfully");
                location.reload(true);
            } else {
                alert(data);
            }
        });
});

$(document).on('click', '#forgetBtn', function(e) {
    var email = $("input[name=forgetEmail]").val();
    $.post("requests/index.php?type=forget", {
            email: email,
        },
        function(data) {
            if (data == 0) {
                alert("Please check your email for new password.");
                $('#forget').modal('hide');
                $('#login').modal('show');
				$("input[name=forgetEmail]").val("");
            } else {
                alert(data);
            }
        });
});

$(document).on('click', '#profileBtn', function(e) {
    var logo = $('#profileLogo').prop('files')[0];
    var pass = $("input[name=profilePass]").val();
    var pass1 = $("input[name=profilePass1]").val();
    var token = $("input[name=profileToken]").val();
    var form_data = new FormData();
    form_data.append('pass', pass);
    form_data.append('rePass', pass1);
    form_data.append('token', token);
    form_data.append('logo', logo);
    $.ajax({
        url: 'requests/index.php?type=profile',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(data) {
            if (data == 0) {
                alert("Your profile has been updated successfully.");
				location.reload(true);
                $('#profile').modal('hide');
                $("input[name=profilePass]").val("");
                $("input[name=profilePass1]").val("");
            } else {
                alert(data);
            }
        }
    });
});

$(document).on('click', '#conBtn', function(e) {
    var title = $("input[name=conTitle]").val();
    var msg = $("textarea[name=conMsg]").val();
    $.post("requests/index.php?type=contact", {
            title: title,
            msg: msg
        },
        function(data) {
            if (data == 0) {
                alert("Your message has been sent successfully, We will get in touch soon.");
                $('#contactUs').modal('hide');
                $("input[name=conTitle]").val("")
                $("textarea[name=conMsg]").val("")
            } else {
                alert(data);
            }
        });
});

$(document).on('click', '#reqBtn', function(e) {
    var title = $("input[name=reqTitle]").val();
    var imdb = $("input[name=reqIMDb]").val();
    var msg = $("textarea[name=reqMsg]").val();
    $.post("requests/index.php?type=request", {
            title: title,
            imdb: imdb,
            msg: msg
        },
        function(data) {
            if (data == 0) {
                alert("Your request has been sent successfully, We will get in touch soon.");
                $('#request').modal('hide');
                $("input[name=reqTitle]").val("");
                $("input[name=reqTitle]").val("");
                $("textarea[name=reqMsg]").val("");
            } else {
                alert(data);
            }
        });
});

$(document).on('click', '.likedVidoes', function(e) {
    var id = $(this).attr("id");
    $.post("requests/index.php?type=likedVidoes", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("Video has been added to your like list.");
            } else {
                alert(data);
				$.post("requests/index.php?type=likedVidoes", {
                        type: "get",
                    },
                    function(data) {
                        $("#likedVidoesBody").html("");
                        $("#likedVidoesBody").append(data);
                    });
            }
        });
});

$(document).on('click', '#likedVideosLabel', function(e) {
    $.post("requests/index.php?type=likedVidoes", {
            type: "get",
        },
        function(data) {
            $("#likedVidoesBody").html("");
            $("#likedVidoesBody").append(data);
        });
});

$(document).on('click', '.disLikedVidoes', function(e) {
    var id = $(this).attr("id");
    $.post("requests/index.php?type=disLikedVidoes", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("Video has been added to your dis like list.");
            } else {
                alert(data);
				$.post("requests/index.php?type=disLikedVidoes", {
                        type: "get",
                    },
                    function(data) {
                        $("#disLikedVidoesBody").html("");
                        $("#disLikedVidoesBody").append(data);
                    });
            }
        });
});

$(document).on('click', '#disLikedVideosLabel', function(e) {
    $.post("requests/index.php?type=disLikedVidoes", {
            type: "get",
        },
        function(data) {
            $("#disLikedVidoesBody").html("");
            $("#disLikedVidoesBody").append(data);
        });
});

$(document).on('click', '.addToMyList', function(e) {
    var id = $(this).attr("id");
    $.post("requests/index.php?type=myList", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("You have added this show to your list successfully.");
            } else {
                alert(data);
                $.post("requests/index.php?type=myList", {
                        type: "get",
                    },
                    function(data) {
                        $("#mainBody").html("");
                        $("#mainBody").append(data);
                    });
            }
        });
});

$(document).on('click', '#historyLabel', function(e) {
    $.post("requests/index.php?type=history", {
            type: "get",
        },
        function(data) {
            $("#historyBody").html("");
            $("#historyBody").append(data);
        });
});

$(document).on('click', '#homeBtn', function(e) {
	$("#loading-screen").show();
	$(".bi-gear-fill").attr("style","color:black");
	$(".bi-film").attr("style","color:black");
	$(".bi-tv").attr("style","color:black");
	$(".bi-house").attr("style","color:#9f8d5c");
	$(".bi-emoji-dizzy").attr("style","color:black");
	$(".bi-search").attr("style","color:black");
    $.post("requests/index.php?type=home", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				// Code inside this function will be executed after the scroll animation is complete
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#tvBtn', function(e) {
	$("#loading-screen").show();
	$(".bi-gear-fill").attr("style","color:black");
	$(".bi-film").attr("style","color:black");
	$(".bi-tv").attr("style","color:#9f8d5c");
	$(".bi-house").attr("style","color:black");
	$(".bi-emoji-dizzy").attr("style","color:black");
	$(".bi-search").attr("style","color:black");
    $.post("requests/index.php?type=home&collection=last_eps", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				// Code inside this function will be executed after the scroll animation is complete
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#movieBtn', function(e) {
	$("#loading-screen").show();
	$(".bi-gear-fill").attr("style","color:black");
	$(".bi-film").attr("style","color:#9f8d5c");
	$(".bi-tv").attr("style","color:black");
	$(".bi-emoji-dizzy").attr("style","color:black");
	$(".bi-house").attr("style","color:black");
	$(".bi-search").attr("style","color:black");
    $.post("requests/index.php?type=home&collection=last_films", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				// Code inside this function will be executed after the scroll animation is complete
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#animeBtn', function(e) {
	$("#loading-screen").show();
	$(".bi-gear-fill").attr("style","color:black");
	$(".bi-film").attr("style","color:black");
	$(".bi-emoji-dizzy").attr("style","color:#9f8d5c");
	$(".bi-tv").attr("style","color:black");
	$(".bi-house").attr("style","color:black");
	$(".bi-search").attr("style","color:black");
    $.post("requests/index.php?type=home&collection=last_eps&category=مسلسلات-انمي", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				// Code inside this function will be executed after the scroll animation is complete
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '.loadMoreBtn', function(e) {
	$("#loading-screen").show();
	$(".loadMoreBtn").hide();
	var numbers = $(this).attr("id");
	var collection = $(".getCollection").attr("id");
    $.post("requests/index.php?type=loadMore&collection="+collection, {
            type: "get",
            more: parseInt(numbers)+1,
        }, 
        function(data) {
			$("#loading-screen").hide();
            $("#content").append(data);
        });
});

$(document).on('click', '#searchBtn', function(e) {
	$(".bi-gear-fill").attr("style","color:black");
	$(".bi-film").attr("style","color:black");
	$(".bi-tv").attr("style","color:black");
	$(".bi-house").attr("style","color:black");
    $(".bi-emoji-dizzy").attr("style","color:black");
	$(".bi-search").attr("style","color:#9f8d5c");
    $("#mainBody").html("<div class='p-3' id='searchBar' style='direction:ltr'><div class='input-group'><div class='input-group-prepend'><button class='btn btn-secondary' type='button' id='searchButton' style='background-color: #9f8d5c;'>بحث</button></div><input class='form-control' id='searchText' placeholder='إدخل كلمة البحث...' style='border-color: #9f8d5c;color: #9f8d5c;'></div></div><div id='searchBody'></div>");
});

$(document).on('click', '#searchButton', function(e) {
    var search = $("input[id=searchText]").val();;
	$("#loading-screen").show();
    $.post("requests/index.php?type=search", {
            type: "get",
            search: search
        },
        function(data) {
			$("#loading-screen").hide();
            $("#searchBody").html("");
            $("#searchBody").append(data);
        });
});

function sendIdToIframe(id) {
    var iframe = document.getElementById('frame');
	iframe.src = "";
    var urlWithId = id;
    iframe.src = urlWithId;
}