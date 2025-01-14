///
/// 公用的JS调用方法：常规的获取游戏、平台、区服等
///
/// create by 李鹏辉

/**
 * 获取日志记录类型
 * @param documentid
 * @param type 1 移除 2 获取
 * @param selectedId
 * @param url
 */
function getSrcType(documentid,gameId,type,selectedId,url)
{
    // console.log(gameId+" "+type);
    $(documentid).empty();
    $.ajax({
        type: 'get',
        data: {
            gameId:gameId,
            type:type
        },
        dataType: "json",
        url: (url==null?"":url)+"../src/get-src",
        async: true,
        success: function(data) {
            $.each(data, function(i) {
                if (data[i].id==selectedId)
                {
                    $("<option selected = 'selected' value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }else{
                    $("<option value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }

            });
            $(documentid).selectpicker('refresh');
        },
        error: function(data) {
            // alert('获取数据失败');
            console.log("获取数据失败")
        }
    });
}
/**
 * 获取游戏列表
 * @param documentid
 * @param async 是否异步获取
 */
function getGame(documentid, async,selectedId,url) {
    $(documentid).empty();
    $.ajax({
        type: 'post',
        data: {},
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-games",
        async: true,
        success: function(data) {
            $.each(data, function(i) {
                if (data[i].id==selectedId)
                {
                    $("<option selected = 'selected' value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }else{
                    $("<option value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }

            });
            $(documentid).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
/**
 * 根据游戏版本ID获取游戏列表
 * @param documentid
 * @param async
 * @param selectedId
 * @param url
 */
function getGamesByVersion(documentId, versionId,url) {
    $(documentId).empty();
    $.ajax({
        type: 'get',
        data: {
            versionId:versionId,
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-games-by-version",
        async: true,
        success: function(data) {
            $("<option value='0'>选择游戏</option>").appendTo(documentId);
            $.each(data, function(i) {
                $("<option value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentId);
            });
            $(documentId).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
/**
 * 根据游戏ID获取分销渠道
 * @param documentid
 * @param async
 * @param selectedId
 * @param url
 */
function getDistribution(documentId, game,distributor,url) {
    $(documentId).empty();
    $.ajax({
        type: 'get',
        data: {
            gameId:game,
            distributorId:distributor
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-distribution",
        async: true,
        success: function(data) {
            $.each(data, function(i) {
                var platform="安卓";
                if (data[i].platform==2)
                {
                    platform="IOS";
                }
                $("<option value='" + data[i].id + "'>" + data[i].name + "("+platform+")</option>").appendTo(documentId);
            });
            $(documentId).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
/**
 * 获取区服列表信息
 * @param documentid 对象
 * @param async 是否异步
 * @param gid 游戏id
 * @param pid 平台id
 * @param url 目录追加
 */
function getServers(documentid,withOutMerged,gameId,distributorId,selectedid,url) {
    //var async = arguments[1] ? arguments[1] : false;
    $(documentid).empty();
    $.ajax({
        type: 'get',
        data: {
            gameId: gameId,
            distributorId: distributorId,
            withOutMerged:withOutMerged
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-server",
        async: true,
        success: function(data) {
            $.each(data, function(i) {
                if(selectedid==data[i].id )
                {
                    $("<option selected = 'selected' value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }else{
                    $("<option value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }
            });
            $(documentid).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
function getDistributor(documentid,async,gameId,selectedId,url) {
    $(documentid).empty();
    $.ajax({
        type: 'get',
        data: {
            gameId: gameId
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-distributor",
        async: true,
        success: function(data) {
            $.each(data, function(i) {
                //console.log("共有渠道:",data.length)
                var selected=selectedId==data[i].id || data.length==1;
                if (selected)
                {
                    $("<option selected = 'selected' value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                    $(documentid).trigger("onchange");
                }else{
                    $("<option value='" + data[i].id + "'>" + data[i].name + "</option>").appendTo(documentid);
                }
            });
            $(documentid).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
function getItemsByGame(documentid,gameId,url)
{
    var async = arguments[1] ? arguments[1] : false;
    $("#"+documentid).empty();
    $.ajax({
        type: 'get',
        data:{
            gameId:gameId
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-items",
        async: true,
        success: function(data) {
            console.log(documentid);
            $.each(data, function(i) {
                if(i>0)
                {
                    $("<option value='" + i+ "'>" + data[i] + "</option>").appendTo($("#"+documentid));
                }
            });
            $("#"+documentid).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}
function getItemsByVersion(documentid,versionId,url) {
    var async = arguments[1] ? arguments[1] : false;
    $("#"+documentid).empty();
    $.ajax({
        type: 'get',
        data:{
            versionId:versionId
        },
        dataType: "json",
        url: (url==null?"":url)+"../permission/get-items",
        async: true,
        success: function(data) {
            console.log(documentid);
            $.each(data, function(i) {
                if(i>0)
                {
                    $("<option value='" + i+ "'>" + data[i] + "</option>").appendTo($("#"+documentid));
                }
            });
            $("#"+documentid).selectpicker('refresh');
        },
        error: function(data) {
            alert('获取数据失败');
        }
    });
}