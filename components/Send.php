<?php
require_once('Db.php');

class Send {
    const SHOW_BY_DEFAULT = 6;

    public static function getMessage() {
        $db = Db::getInstance();
        $limit = isset($_SESSION['show']) ? $_SESSION['show'] : self::SHOW_BY_DEFAULT;

        $k_post = $db->query("SELECT COUNT(*) FROM `sms_list_tz`")->fetchColumn();

        if ($k_post == 0) {
            echo "<div>Список пуст</div>";
        }

        $page_number = filter_has_var(INPUT_POST, 'page') ? filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH) : 1;
        $total_pages = ceil($k_post/$limit);
        $page_position = (($page_number-1) * $limit);

        $result = $db->query("SELECT ID, TEXT FROM `sms_list_tz` ORDER BY `id` DESC LIMIT $page_position, $limit");

        echo '<ul class="contents">';
        while ($row = $result->fetch()) {
            echo '<li><div id="item_' . intval($row["ID"]) . '">';
            echo htmlspecialchars($row['TEXT']) . ' <a href="#" id="del-' . intval($row['ID']) . '" class="del_button">Удалить</a> <br />';
            echo '</div></li>';
        }
        echo '</ul>';

        echo self::paginate_function($limit, $page_number, $k_post, $total_pages);

    }

    public static function recordToDelete($id) {
        $db = Db::getInstance();
        $idDel = intval($id);
        if ($db->query("SELECT COUNT(*) FROM `sms_list_tz` WHERE ID = '" . $idDel . "'")->fetchColumn() == true) {
            $db->exec("DELETE FROM `sms_list_tz` WHERE `ID` = '" . $idDel . "'");
        }
    }

    public static function contentText($text) {
        $db = Db::getInstance();
        $db->prepare("INSERT INTO `sms_list_tz`(TEXT) VALUES(?)")->execute([
            $text
        ]);
    }

    public static function paginate_function($item_per_page, $current_page, $total_records, $total_pages) {
        $pagination = '';
        if ($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) { //verify total pages and current page number
            $pagination .= '<ul class="pagination">';

            $right_links = $current_page + 3;
            $previous = $current_page - 3; //previous link
            $next = $current_page + 1; //next link
            $first_link = true; //boolean var to decide our first link

            if ($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>'; //first link
                $pagination .= '<li><a href="#" data-page="' . $previous_link . '" title="Previous">&lt;</a></li>'; //previous link
                for ($i = ($current_page - 2); $i < $current_page; $i ++) { //Create left-hand side links
                    if ($i > 0) {
                        $pagination .= '<li><a href="#" data-page="' . $i . '" title="Page' . $i . '">' . $i . '</a></li>';
                    }
                }
                $first_link = false; //set first link to false
            }

            if ($first_link) { //if current active page is first link
                $pagination .= '<li class="first active">' . $current_page . '</li>';
            } elseif ($current_page == $total_pages) { //if it's the last active link
                $pagination .= '<li class="last active">' . $current_page . '</li>';
            } else { //regular current link
                $pagination .= '<li class="active">' . $current_page . '</li>';
            }

            for ($i = $current_page + 1; $i < $right_links; $i ++) { //create right-hand side links
                if ($i <= $total_pages) {
                    $pagination .= '<li><a href="#" data-page="' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($current_page < $total_pages) {
                $next_link = ($i > $total_pages) ? $total_pages : $i;
                $pagination .= '<li><a href="#" data-page="' . $next_link . '" title="Next">&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="#" data-page="' . $total_pages . '" title="Last">&raquo;</a></li>'; //last link
            }

            $pagination .= '</ul>';
        }
        return $pagination; //return pagination links
    }
}