$(document).ready(function(){
	$('#threeDots, #playVideo, #history, #disLikedVidoesModal, #likedVidoesModal').on('hidden.bs.modal', function (e) {
		$(this).find('.modal-body').empty(); // clear the body of the modal
	});
});

$(document).on('click', '.changeIframeSrc', function(e) {
	$("#loading-screen").show();
    $('#mainBody').html('');
	$('#mainBody').append("<iframe id='frame' src='' style='width: 100%;height: 100vh;' sandbox='allow-same-origin allow-scripts' allowFullScreen></iframe>");
	var link = $(this).attr('id');
	$('#frame').attr('src', link);
	$("#loading-screen").hide();
});

$(document).on('click', '.playVideo', function(e) {
	$("#loading-screen").show();
    var getId = $(this).attr("id");
	var title = $(this).html();
    $.post("requests3/index.php?type=playVideo", {
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
	$("#loading-screen").show();
    var getId = $(this).attr("id");
    //var title = $(".categoryTitle" + getId).html();
    $.post("requests3/index.php?type=more", {
		id: getId
	},
	function(data) {
		$("#loading-screen").hide();
		$("#moreBody").html(data);
		$("#moreTitle").html();
	});
});

$(document).on('click', '.threeDots2', function(e) {
	$("#loading-screen").show();
    $('#threeDots2').modal('hide');
    var getId = $(this).attr("id");
    //var title = $(".categoryTitle" + getId).html();
    $.post("requests3/index.php?type=more", {
		id: getId
	},
	function(data) {
		$("#loading-screen").hide();
        $('#threeDots2').modal('show');
		$("#moreBody2").html(data);
		$("#moreTitle2").html();
	});
});

$(document).on('click', '#regBtn', function(e) {
    var username = $("input[name=regUser]").val();
    var pass = $("input[name=regPass]").val();
    var rePass = $("input[name=regPass1]").val();
    var email = $("input[name=regEmail]").val();
    $.post("requests3/index.php?type=register", {
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
    $.post("requests3/index.php?type=login", {
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
		$("#homeBtn").attr("style","color:#9f8d5c");
		$(".bi-house").addClass("bi-house-fill").removeClass("bi-house");
		$.post("requests3/index.php?type=home", {
			type: "get",
		},
		function(data) {
			$("#loading-screen").hide();
			$("#mainBody").html("");
			$("#mainBody").append(data);
		});
    } else {
		$("#loading-screen").hide();
		$("#homeBtn").attr("style","color:#9f8d5c");
		$(".bi-house").addClass("bi-house-fill").removeClass("bi-house");
        $("#profileOptions0").attr("style", "display:block");
        $("#profileOptions1").attr("style", "display:none");
    }
}

$(document).on('click', '#logoutLabel', function(e) {
    $.post("requests3/index.php?type=logout", {
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
    $.post("requests3/index.php?type=forget", {
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
        url: 'requests3/index.php?type=profile',
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
    $.post("requests3/index.php?type=contact", {
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
    $.post("requests3/index.php?type=request", {
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
    $.post("requests3/index.php?type=likedVidoes", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("Video has been added to your like list.");
            } else {
                alert(data);
				$.post("requests3/index.php?type=likedVidoes", {
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
    $.post("requests3/index.php?type=likedVidoes", {
            type: "get",
        },
        function(data) {
            $("#likedVidoesBody").html("");
            $("#likedVidoesBody").append(data);
        });
});

$(document).on('click', '.disLikedVidoes', function(e) {
    var id = $(this).attr("id");
    $.post("requests3/index.php?type=disLikedVidoes", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("Video has been added to your dis like list.");
            } else {
                alert(data);
				$.post("requests3/index.php?type=disLikedVidoes", {
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
    $.post("requests3/index.php?type=disLikedVidoes", {
            type: "get",
        },
        function(data) {
            $("#disLikedVidoesBody").html("");
            $("#disLikedVidoesBody").append(data);
        });
});

$(document).on('click', '.addToMyList', function(e) {
    var id = $(this).attr("id");
    $.post("requests3/index.php?type=myList", {
            type: "add",
            id: id
        },
        function(data) {
            if (data == 0) {
                alert("You have added this show to your list successfully.");
            } else {
                alert(data);
                $.post("requests3/index.php?type=myList", {
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
    $.post("requests3/index.php?type=history", {
            type: "get",
        },
        function(data) {
            $("#historyBody").html("");
            $("#historyBody").append(data);
        });
});

$(document).on('click', '#homeBtn', function(e) {
	$("#loading-screen").show();

    $("#newsBtn").attr("style","color:#5e5e5e");

	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#9f8d5c");
	$(".bi-house").addClass("bi-house-fill").removeClass("bi-house");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
	
    $.post("requests3/index.php?type=home", {
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

$(document).on('click', '.liveMatch', function(e) {
	$("#loading-screen").show();
	var id = $(this).attr("id");
    $.post("requests3/index.php?type=live&view="+id, {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#newsBtn', function(e) {
	$("#loading-screen").show();
	
	$("#newsBtn").attr("style","color:#9f8d5c");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
    
	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
    $.post("requests/index.php?type=news", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#liveBtn', function(e) {
	$("#loading-screen").show();
	
	$("#liveBtn").attr("style","color:#9f8d5c");

    $("#newsBtn").attr("style","color:#5e5e5e");
	
	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
    $.post("requests/index.php?type=live", {
            type: "get",
        },
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				// Code inside this function will be executed after the scroll animation is complete
				/*setTimeout(function() {
					$("#loading-screen").hide();
				}, 3000);*/
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '#tvBtn', function(e) {
	$("#loading-screen").show();

    $("#newsBtn").attr("style","color:#5e5e5e");

	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#9f8d5c");
	$(".bi-tv").addClass("bi-tv-fill").removeClass("bi-tv");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house-fill").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
	
    $.post("requests3/index.php?type=home&category=seriestv", {
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

    $("#newsBtn").attr("style","color:#5e5e5e"); 

	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#9f8d5c");
	$(".bi-camera-reels").addClass("bi-camera-reels-fill").removeClass("bi-camera-reels");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house-fill").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
	
    $.post("requests3/index.php?type=home&category=movies", {
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

    $("#newsBtn").attr("style","color:#5e5e5e");

	$("#animeBtn").attr("style","color:#9f8d5c");
	$(".bi-emoji-dizzy").addClass("bi-emoji-dizzy-fill").removeClass("bi-emoji-dizzy");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house-fill").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#5e5e5e");
	$(".bi-search-heart-fill").addClass("bi-search-heart").removeClass("bi-search-heart-fill");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
	
    $.post("requests3/index.php?type=home&category=category/مسلسلات-كرتون/", {
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
    $.post("requests3/index.php?type=loadMore&collection="+collection, {
            type: "get",
            more: parseInt(numbers)+1,
        }, 
        function(data) {
			$("#loading-screen").hide();
            $("#content").append(data);
        });
});

$(document).on('click', '.loadMoreNewsBtn', function(e) {
	$("#loading-screen").show();
	$(".loadMoreBtn").hide();
	var numbers = $(this).attr("id");
    $.post("requests/index.php?type=news", {
            type: "get",
            more: parseInt(numbers)+1,
        }, 
        function(data) {
			$("html, body").animate({ scrollTop: 0 }, "slow", function() {
				$("#loading-screen").hide();
				$("#mainBody").html("");
				$("#mainBody").append(data);
			});
        });
});

$(document).on('click', '.loadMoreSearchBtn', function(e) {
	$("#loading-screen").show();
	$(".loadMoreSearchBtn").hide();
	var numbers = $(this).attr("id");
	var search = $(".getSearch").attr("id");
    $.post("requests3/index.php?type=loadMoreSearch&search="+search, {
            type: "get",
            more: parseInt(numbers)+1,
        }, 
        function(data) {
			$("#loading-screen").hide();
            $("#content").append(data);
        });
});

$(document).on('click', '#searchBtn', function(e) {

    $("#newsBtn").attr("style","color:#5e5e5e");
    
	$("#animeBtn").attr("style","color:#5e5e5e");
	$(".bi-emoji-dizzy-fill").addClass("bi-emoji-dizzy").removeClass("bi-emoji-dizzy-fill");
	
	$("#settingBtn").attr("style","color:#5e5e5e");
	$(".bi-gear-fill").addClass("bi-gear").removeClass("bi-gear-fill");
	
	$("#movieBtn").attr("style","color:#5e5e5e");
	$(".bi-camera-reels-fill").addClass("bi-camera-reels").removeClass("bi-camera-reels-fill");
	
	$("#tvBtn").attr("style","color:#5e5e5e");
	$(".bi-tv-fill").addClass("bi-tv").removeClass("bi-tv-fill");
	
	$("#homeBtn").attr("style","color:#5e5e5e");
	$(".bi-house-fill").addClass("bi-house").removeClass("bi-house-fill");
	
	$("#searchBtn").attr("style","color:#9f8d5c");
	$(".bi-search-heart").addClass("bi-search-heart-fill").removeClass("bi-search-heart");
	
	$("#liveBtn").attr("style","color:#5e5e5e");
	
    $("#mainBody").html("<div class='p-3' id='searchBar' style='direction:ltr'><div class='input-group'><div class='input-group-prepend'><button class='btn btn-secondary' type='button' id='searchButton' style='background-color: #9f8d5c;'>بحث</button></div><input class='form-control' id='searchText' placeholder='إدخل كلمة البحث...' style='border-color: #9f8d5c;color: #9f8d5c;'><select class='custom-select' id='searchType' style='border-color: #9f8d5c;color: #9f8d5c;'><option value=''>فلم</option><option value='/list/series/'>مسلسل</option><option value='/list/anime/'>أنيمي</option></select></div></div><div id='searchBody'></div>");
});

$(document).on('keyup', '#searchText', function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        $("#searchButton").click();
    }
});

$(document).on('click', '#searchButton', function(e) {
    var search = $("input[id=searchText]").val();
    var searchType = $("select[id=searchType]").val();
	$("#loading-screen").show();
    $.post("requests3/index.php?type=search", {
            type: "get",
            search: search,
            searchType: searchType
        },
        function(data) {
			$("#loading-screen").hide();
            $("#searchBody").html("");
            $("#searchBody").append(data);
        });
});

$(document).on('click', '.categoryTitleTv', function(e) {
	$("#loading-screen").show();
	var id = $(this).attr("id");
	
    $.post("requests3/index.php?type=home&genre="+id, {
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

$(document).on('click', '.categoryTitlePost', function(e) {
	$("#loading-screen").show();
	var id = $(this).attr("id");
    $.post("requests3/index.php?type=home&genre="+id, {
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

$(document).on('click', '.categoryTitleMovie', function(e) {
	$("#loading-screen").show();
	var id = $(this).attr("id");
    $.post("requests3/index.php?type=home&genre="+id, {
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

function sendIdToIframe(id) {
    var iframe = document.getElementById('frame');
	iframe.src = "";
    var urlWithId = id;
    iframe.src = urlWithId;
}

function sendIdToIframe2(id) {
    var iframe = document.getElementById('frame');
    iframe.src = "";
    var urlWithId = id;
        if (urlWithId.toLowerCase().indexOf('wecima') === -1) {
        urlWithId = 'videoPlayer.php?link=' + encodeURIComponent(urlWithId);
    }
    iframe.src = urlWithId;
}

$(document).on('click', '.playServer', function(e) {
	$("#loading-screen").show();
	var id = $(this).attr("id");
    var iframe = document.getElementById('frame');
    iframe.src = "";
    $.post("requests3/index.php?type=getServer", {
            type: "get",
            data: id,
        },
        function(data) {
			$("#loading-screen").hide();
            iframe.src = data;
        }
    );
});