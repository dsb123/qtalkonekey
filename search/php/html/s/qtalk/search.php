<?php

/**
 * Created by PhpStorm.
 * User: may
 * Date: 2016/10/27
 * Time: 上午11:07
 */
class Action
{
//QTALK_OPEN_USER_VCARD = 0         // 打开单人名片
//QTALK_OPEN_GROUP_VCARD = 1        // 打开群组名片
//
//QTALK_COMELHELP_QUSER = 2         // 打开骆驼帮查人
//QTALK_PUBLIC_ACCOUNT = 3          // 打开公众号
//QTALK_WEBVIEW = 4                 // 打开webview，渲染url
    

const QTALK_OPEN_USER_VCARD = 0;      // 打开单人名片
const QTALK_OPEN_GROUP_VCARD = 1;        // 打开群组聊天
const QTALK_OPEN_FRIENDS_VC = 2;        // 打开好友
const QTALK_OPEN_GROUPS_VC = 3;        // 打开群组
const QTALK_OPEN_UNREAD_MESSAGE = 4;        // 打开未读消息
const QTALK_OPEN_PUBLIC_ACCOUNT = 5;        // 打开公众号
const QTALK_WEBVIEW = 6;               // 打开webview，渲染url
const QTALK_OPEN_USER_CHAT = 7;               // 打开单人聊天
const QTALK_OPEN_PUBLIC_VCARD = 8;               // 打开公众号名片
}

require_once("../../../common/ckeyAuth.php");
require_once("../../../common/userlib_he.php");
require_once("../../../common/define.php");

$requestJson = file_get_contents("php://input");

$userGroup = 'Q01';
$groupGroup = 'Q02';
$singlekeywordGroup = 'Q05';
$muckeywordGroup = 'Q06';
$commonGroup = 'Q07';
$GroupDetail = 'Q10';

$requestData = json_decode($requestJson, true);

$offset = $requestData['start'];
$limit = $requestData['length'];
$username = $requestData['key'];
$userId = $requestData['qtalkId'];
$groupId = $requestData['groupId'];
$ckey = $requestData['cKey'];

$whiteList = array('10.86.2.170', '10.86.218.4', '10.86.218.5', '10.86.43.203', '10.90.181.78');


if (!UserLib::inIPWhiteList($whiteList) &&
    ((strlen($ckey) > 0 && !CKeyAuthorization::checkCkeyString($ckey))
        || !isset($requestData['cKey']))) {
    echo header("Content-type: text/html; charset=utf-8");
    echo json_encode(array(
            'errcode' => 533,
            'msg' => '拒绝访问!',
            'msg1' => UserLib::getIP(),
            'key' => $ckey,
        )
    );
    exit();
} else {
    if (strlen($username) >= 2) {
        $conn = @pg_connect(Environment::sharedInstance()->get_db_string(dbKey::$qtalkDB_readonly));

if ($groupId) {
            if ($groupId == $userGroup) {
                $userArray = UserLib::searchUser($conn, $username, $limit + 1, $offset);

                $itemCount = count($userArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($userArray);
                } else {
                    $hasMore = false;
                }
                $result = array(
                    'errcode' => 0,
                    'msg' => '',
                    'data' => array(
                        array(
                            'groupLabel' => '联系人列表',
                            'groupId' => $userGroup,
                            'groupPriority' => 0,
                            'todoType' => Action::QTALK_OPEN_USER_VCARD,
                            'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/ff1a003aa731b0d4e2dd3d39687c8a54.png',
                            'hasMore' => $hasMore,
                            'info' => $userArray,
                        ),
                    ),
                );
                echo header("Content-type: text/html; charset=utf-8");
                echo json_encode($result);
            } elseif ($groupId == $groupGroup) {
                if ($userId)
                    $groupArray = UserLib::searchGroup($conn, $userId, $username, $limit + 1, $offset);

                $itemCount = count($groupArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($groupArray);
                } elseif ($itemCount <= 0) {
                    $groupArray = UserLib::searchGroupByUsers($conn, $userId, $username, $limit + 1, $offset);
                    $itemCount = count($groupArray);
                    if ($itemCount > $limit) {
                        $hasMore = true;
                        array_pop($groupArray);
                    } else {
                        $hasMore = false;
                    }
                } else {
                    $hasMore = false;
                }
                $result = array(
                    'errcode' => 0,
                    'msg' => '操作成功',
                    'data' => array(
                        array(
                            'groupLabel' => '群组列表',
                            'groupId' => $groupGroup,
                            'groupPriority' => 0,
                            'todoType' => Action::QTALK_OPEN_GROUP_VCARD,
                            'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/bc0fca9b398a0e4a1f981a21e7425c7a.png',
                            'hasMore' => $hasMore,
                            'info' => $groupArray,
                        ),
                    ),
                );
                echo header("Content-type: text/html; charset=utf-8");
                echo json_encode($result);
            } elseif ($groupId == $commonGroup) {
                if ($userId)
                    $commonmucArray = UserLib::searchGroupbysingleuser($conn, $userId, $username, $limit + 1, $offset);
$itemCount = count($commonmucArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($commonmucArray);
                }
                else {
                    $hasMore = false;
                }
                $result = array(
                    'errcode' => 0,
                    'msg' => '操作成功',
                    'data' => array(
                        array(
                            'groupLabel' => '共同群组',
                            'groupId' => $commonGroup,
                            'groupPriority' => 0,
                            'todoType' => Action::QTALK_OPEN_GROUP_VCARD,
                            'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/bc0fca9b398a0e4a1f981a21e7425c7a.png',
                            'hasMore' => $hasMore,
                            'info' => $commonmucArray,
                        ),
                    ),
                );
                echo header("Content-type: text/html; charset=utf-8");
                echo json_encode($result);
            }else {
                $result = array(
                    'errcode' => 500,
                    'msg' => '无法预期的groupId',
                );
                echo header("Content-type: text/html; charset=utf-8");
                echo json_encode($result);
            }
        } else {
            $userArray = UserLib::searchUser($conn, $username, $limit + 1, $offset);
            $groupArray = null;
            $commongroupArray = null;
            if ($userId) {
                $groupArray = UserLib::searchGroup($conn, $userId, $username, $limit + 1, $offset);
                $commongroupArray = UserLib::searchGroupbysingleuser($conn, $userId, $username, $limit + 1, $offset);
            }

            $dataArray = array();

            if ($userArray) {

                $itemCount = count($userArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($userArray);
                } else {
                    $hasMore = false;
                }

//            array_unshift($userArray, array(
//                    'icon' => "http://img1.qunarzz.com/p/tts6/1409/16/a0a61ac61c44ac83ffffffffc8d65eac.jpg_r_390x260x90_b2e04f6b.jpg",
//                    'label' => "<B>【跟团游】【 普吉岛皇冠卖家】白天0自费，送大堡礁浮潜+骑大象+按摩+人妖秀+旅意险！</B>",
//                    'content' => "<span style=\"color:red\">普吉岛一地 | 玩水的胜地（浮潜，漫步海滩，香蕉船）全程五星 | 团号: 1521824155 </span>",
//                    'uri' => "http://yxlt2.package.qunar.com/user/detail.jsp?id=1521824155&tm=2lou_tejia_origin",
//                    'todoType' => 6,
//            )
//            );

                array_push($dataArray, array(
                    'groupLabel' => '联系人列表',
                    'groupId' => $userGroup,
'groupPriority' => 0,
                    'todoType' => Action::QTALK_OPEN_USER_VCARD,
                    'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/ff1a003aa731b0d4e2dd3d39687c8a54.png',
                    'hasMore' => $hasMore,
                    'info' => $userArray,
                ));
            }


            if ($groupArray) {

                $itemCount = count($groupArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($groupArray);
                } else {
                    $hasMore = false;
                }

                array_push($dataArray, array(
                    'groupLabel' => '群组列表',
                    'groupId' => $groupGroup,
                    'groupPriority' => 1,
                    'todoType' => Action::QTALK_OPEN_GROUP_VCARD,
                    'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/bc0fca9b398a0e4a1f981a21e7425c7a.png',
                    'hasMore' => $hasMore,
                    'info' => $groupArray,
                ));
            }

        if ($commongroupArray) {

                $itemCount = count($commongroupArray);
                if ($itemCount > $limit) {
                    $hasMore = true;
                    array_pop($commongroupArray);
                } else {
                    $hasMore = false;
                }

                array_push($dataArray, array(
                    'groupLabel' => '共同群组',
                    'groupId' => $commonGroup,
                    'groupPriority' => 2,
                    'todoType' => Action::QTALK_OPEN_GROUP_VCARD,
                    'defaultportrait' => 'https://qt.qunar.com/file/v2/download/perm/bc0fca9b398a0e4a1f981a21e7425c7a.png',
                    'hasMore' => $hasMore,
                    'info' => $commongroupArray,
                ));
            }


        $result = array(
                'errcode' => 0,
                'msg' => '',
                'data' => $dataArray,
            );
            echo header("Content-type: text/html; charset=utf-8");
            echo json_encode($result);
        }
    } else {
        $result = array(
            'errcode' => 500,
            'msg' => '字符串长度需要大于1',
        );
        echo header("Content-type: text/html; charset=utf-8");
  echo json_encode($result);
    }
}
