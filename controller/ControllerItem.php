<?php

use PSpell\Config;

require_once  "framework/Controller.php";
require_once "framework/Configuration.php";
require_once "model/Item.php";
require_once "framework/Tools.php";
require_once "model/User.php";
require_once "utils/ImageUtils.php";
require_once "utils/EncoderDecoder.php";


class ControllerItem extends Controller
{

    public function index(): void
    {
        $this->browse();
    }


    public function items(): void
    {
        // le bouton back dans open from-items renvoie vers item/items
        $search_criteria = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "") { // si on renvoie un critere de recherche donc item/items/criteria_encoded
            $search_criteria = $_GET["param1"];
            $this->redirect("item", "list", $search_criteria); // rediriger vers item/list/criteria_encoded
        } else {
            $this->redirect("item", "list"); // rediriger vers item/list
        }
    }


    public function browse(): void
    {
        $user = $this->get_user_or_false();
        $from = "from-browse";
        $search_criteria = "";

        if ($user != false) { // si $user == false nous sommes en mode invité
            $items_participated_in = Item::get_items_participatedIn($user->get_id());
            $other_available_items = Item::get_other_available_item($user->get_id());
        } else {
            $items_participated_in = [];
            $other_available_items = Item::get_other_available_item(-1);    // aucun user a l'id -1
        }

        if (isset($_GET["param1"]) && $_GET["param1"] != "") { // si on arrive sur browse avec un critere de recherche
            $search_criteria = EncoderDecoder::url_safe_decode($_GET["param1"]); // decodage du critere de recherche pour le mettre dans le champs de recherche
        }

        (new View("item_browse"))->show(["items_participated_in" => $items_participated_in, "other_available_items" => $other_available_items, "user" => $user, "from" => $from, "search_criteria" => $search_criteria]);
    }


    public function open(): void
    {
        $user = $this->get_user_or_false();

        $from = "";
        $priority = 1;
        // url de navigation preconstruits pour ne pas mettre trop de logique d'URL dans la vue
        $back_url = "item";
        $bids_url = "item/bid/";
        $edit_item_url = "item/edit/";
        $manage_images_url = "item/manage_images/";
        // recuperera le critere de recherche
        $search_criteria = "";

        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) {
            $item = Item::get_item_by_id($_GET["param1"]);
            $bids_url = $bids_url . $item->get_id();
            $edit_item_url = $edit_item_url . $item->get_id();
            $manage_images_url = $manage_images_url . $item->get_id();
            $pictures = $item->get_pictures();

            if (isset($_GET["param2"])) {
                $from = substr(stristr($_GET["param2"], "-"), 1);
                $back_url = $back_url . "/" . $from;
                if (isset($_GET["param3"]) && is_numeric($_GET["param3"])) {
                    $priority = $_GET["param3"];
                } else if (isset($_GET["param3"]) && $_GET["param3"] != "") {
                    $search_criteria = $_GET["param3"];
                    $back_url = $back_url . "/" . $search_criteria;
                    $bids_url = $bids_url . "/" . $search_criteria;
                    $edit_item_url = $edit_item_url . "/" . $search_criteria;
                    $manage_images_url = $manage_images_url . "/" . $search_criteria;
                }
            }
        } else {
            throw new Exception("No Item specified or Item not found");
        }
        if (!$item->is_open_item() && $user->get_id() !=  $item->get_owner() &&  (count($item->get_bids()) == 0 || $user->get_id() != $item->get_bids()['0']->owner)) {
            throw new Exception("This Item seem to not be yours.");
        } else {
            $owner_picture = User::get_path_picture($item->get_owner());
            $owner_role = User::get_role_by_id($item->get_owner());

            (new View("item_open"))->show([
                "item" => $item,
                "owner_picture" => $owner_picture,
                "owner_role" => $owner_role,
                "pictures" => $pictures,
                "user" => $user,
                "from" => $from,
                "priority" => $priority,
                "back_url" => $back_url,
                "bids_url" => $bids_url,
                "edit_item_url" => $edit_item_url,
                "manage_images_url" => $manage_images_url,
                "search_criteria" => $search_criteria
            ]);
        }
    }

    public function bid(): void
    {
        $user = $this->get_user_or_redirect();
        $current_date = AppTime::get_current_datetime();
        $search_criteria = "";
        if ($_POST) {
            $item_id = $_GET['param1'];
            $amount = ($_POST['buy']) ? $_POST['buy'] : $_POST['min_bid'];

            Item::buy($user->get_id(), $item_id, $current_date, $amount);

            // on regarde si le critere de recherce a ete envoye
            if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                $search_criteria = $_GET["param2"];
            }

            // comme on sait que la seule maniere qu'on puisse bid est par browse, donc on met le from-browse dans le from
            $this->redirect("item", "open", $item_id, "from-browse", $search_criteria);
        }
        $this->index();
    }

    private function build_errors_array(): array
    {
        $errors[] = [];
        $errors['title'] = [];
        $errors['description'] = [];
        $errors['duration_days'] = [];
        $errors['starting_bid'] = [];
        $errors['buy_now_price'] = [];
        $errors['saleprice'] = [];
        return $errors;
    }
    private function build_info_array(Item | null  $item, bool $first): array
    {
        $info = [];

        if ($item !== null) {

            $info['id'] = $item->get_id();
            $info['title'] = $first ? $item->get_title() : Tools::sanitize($_POST['title']);
            $info['description'] = $first ? $item->get_description() : Tools::sanitize($_POST['description']);
            $info['duration_days'] = $first ? $item->get_duration_days() : Tools::sanitize($_POST['duration_days']);;
            $info['starting_bid'] = $first ?  $item->get_starting_bid() : Tools::sanitize($_POST['starting_bid']);;
            if ($item->get_starting_bid() != null) {
                $info['buy_now_price'] = $first ? $item->get_buy_now_price() : $info['buy_now_price'] = Tools::sanitize($_POST['buy_now_price']);;
                $info['saleprice'] = $first ? null :  $info['saleprice'] = Tools::sanitize($_POST['saleprice']);
            } else {
                $info['saleprice'] =  $first ? $item->get_buy_now_price() : $info['saleprice'] = Tools::sanitize($_POST['saleprice']);
                $info['buy_now_price'] = $first ? null : $info['buy_now_price'] = Tools::sanitize($_POST['buy_now_price']);
            }
        } else {

            $info['id'] = 0;
            $info['title'] = $first ? null : Tools::sanitize($_POST['title']);
            $info['description'] = $first ? null : Tools::sanitize($_POST['description']);
            $info['duration_days'] = $first ? null : Tools::sanitize($_POST['duration_days']);
            $info['starting_bid'] = $first ? null : Tools::sanitize($_POST['starting_bid']);
            $info['buy_now_price'] = $first ? null : Tools::sanitize($_POST['buy_now_price']);
            $info['saleprice'] = $first ? null : Tools::sanitize($_POST['saleprice']);
        }

        return $info;
    }

    public function edit(): void
    {
        $action = 'edit';
        $user = $this->get_user_or_redirect();
        $back_url_to_open = "item/open/";
        $search_criteria = "";

        if (!isset($_GET['param1']) || !is_numeric($_GET['param1'])) {
            $this->index();
        } else {
            $item = Item::get_item_by_id($_GET['param1']);
            // si item n'est  pas celui de l'utilisateur
            if ($item->get_owner() != $user->get_id() || !$item->is_open_item() || count($item->get_bids()) != 0) {
                throw new Exception("you can't edit this ");
                return;
            }
        }
        // si pas de post, on vient d'arriver sur la page donc on va vers la vue item/edit
        if (!$_POST) {
            $item = Item::get_item_by_id($_GET['param1']);

            $back_url_to_open = $back_url_to_open . $item->get_id() . "/from-items"; // on dit que l'url de back est item/open/item_id/from-items 
            // si on a recu un critere de recherche
            if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                $search_criteria = $_GET["param2"];
                $back_url_to_open = $back_url_to_open . "/" . $search_criteria; // url de back devient item/open/item_id/from-items/search_criteria
            }
            $errors = $this::build_errors_array();
            $info = $this::build_info_array($item, true);

            (new View('item_edit'))->show(['info' => $info, 'errors' => $errors, 'action' => $action, "back_url_to_open" => $back_url_to_open, "search_criteria" => $search_criteria, "user" => $user]);
        } else { // si y'a eu un post, donc qu'on a appuye sur le bouton save

            $info = $this::build_info_array($item, false); // recherche des infos sur l'item
            $errors = $this::build_errors_array(); // creation du tableau d'erreur
            $errors = Item::validat_item($user->get_id(), $info, $errors); // validation de l'item
            if (count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days']) == 0 && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0) { // si l'item respecte les regles metier
                Item::put_edition_item($user->get_id(), $info); // mise a jour dans la DB
                // si on a recu un critere de recherche
                if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                    $search_criteria = $_GET["param2"];
                    $this->redirect("item", "open", $info['id'], "from-items", $search_criteria); // redirection vers item/open/id/from-items/search_criteria
                } else {
                    $this->redirect("item", "open", $info['id'], "from-items"); // redirection vers item/open/id/from-items
                }
            } else { // si item ne respecte pas les regles metiers, on recharge la page avec les erreurs

                $back_url_to_open = "item/open/" . $info['id'] . "/from-items";
                // si on a recu un critere de recherche
                if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                    $search_criteria = $_GET["param2"];
                    $back_url_to_open = $back_url_to_open . "/" . $search_criteria; // url de back devient item/open/item_id/from-items/search_criteria
                }

                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'action' => $action, "back_url_to_open" => $back_url_to_open, "search_criteria" => $search_criteria, "user" => $user]);
            }
        }
    }


    public function add(): void
    {
        $action = 'add';
        $user = $this->get_user_or_redirect();
        $errors = $this::build_errors_array();
        if (!$_POST) {
            $info = $this::build_info_array(null, true);
            (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'action' => $action, "user" => $user]);
        } else {
            $info = $this::build_info_array(null, false);
            $errors = Item::validat_item($user->get_id(), $info, $errors);
            if (
                count($errors['title']) == 0 && count($errors['description']) == 0 &&  count($errors['duration_days']) == 0
                && count($errors['saleprice']) == 0 && count($errors['buy_now_price']) == 0 && count($errors['starting_bid']) == 0
            ) {
                $info['id'] = Item::put_edition_item($user->get_id(), $info);
                $this->redirect("item", "open", $info['id']);
            } else {
                (new View("item_edit"))->show(['info' => $info, 'errors' => $errors, 'action' => $action, "user" => $user]);
            }
        }
    }



    public function list(): void
    {
        $user = $this->get_user_or_redirect();
        $active_items = Item::get_user_active_item($user->get_id());
        $closed_unsold_items = Item::get_user_closed_unsold_item($user->get_id());
        $sold_items = Item::get_user_sold_item($user->get_id());
        $from = "from-items";

        //critere de recherche
        $search_criteria = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "") { // si on arrive avec item/list/criteria_encoded
            $search_criteria = EncoderDecoder::url_safe_decode($_GET["param1"]); //on decode le critere de recherche
        }
        (new View("item_list"))->show(["active_items" => $active_items, "closed_unsold_items" => $closed_unsold_items, "sold_items" => $sold_items, "user" => $user, "from" => $from, "search_criteria" => $search_criteria]);
    }


    public function delete(): void
    {
        $user = $this->get_user_or_redirect();
        $search_criteria = "";
        if (isset($_GET['param1']) && is_numeric($_GET['param1'])) {
            $item_id = Tools::sanitize($_GET['param1']);
            $item = Item::get_item_by_id($item_id);

            if (isset($_GET["param2"]) && $_GET["param2"] != "") { // si critere de recherche
                $search_criteria = $_GET["param2"];
            }

            if ($item->get_owner() != $user->get_id() || count($item->get_bids()) != 0 || !$item->is_open_item()) {
                throw new Exception("You can't delete this item ");
            }
            $owner = User::get_full_name_by_id($item->get_owner());
            (new View("delete_confirm"))->show(["owner" => $owner, "title" => $item->get_title(), "id" => $item_id, "search_criteria" => $search_criteria]);
        }
    }
    public function delete_item(): void
    {
        $user = $this->get_user_or_redirect();
        $item_id = Tools::sanitize($_GET['param1']);
        $item = Item::get_item_by_id($item_id);

        $search_criteria = "";
        if (isset($_GET["param2"]) && $_GET["param2"] != "") { // si critere de recherche
            $search_criteria = $_GET["param2"];
        }
        if ($item->get_owner() != $user->get_id() || count($item->get_bids()) != 0 || !$item->is_open_item()) {
            throw new Exception("You can't delete this item ");
        } else {
            $this->delete_item_files($item);
            $item->delete();
            $this->redirect('item', 'list', $search_criteria);
        }
    }

    private function delete_item_files(Item $item): void
    {
        foreach ($item->get_pictures() as $picture) {
            ImageUtils::delete_image_files($picture->get_path());
        }
        rmdir("uploads/items/" . $item->get_id());
    }


    public function manage_images(): void
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        $search_criteria = ""; // critere de recherche

        if (isset($_GET['param1']) && is_numeric($_GET['param1']) && $user->is_item_owner($_GET['param1'])) {    // pas oublier de check aussi si l'objet appartient au user connecte et que l'item est actif
            $item_id = $_GET['param1'];
            $item = Item::get_item_by_id($item_id);

            if (isset($_GET["param2"]) && $_GET["param2"] != "") { // si on a un critere de recherche
                $search_criteria = $_GET["param2"];
            }

            if (!$item->is_open_item()) // si l'item n'est pas un item actif, redirection vers index d'item
                $this->redirect("item");
            if (isset($_FILES["image"]) && $_FILES["image"]["name"] != "") { // si on a upload une image
                $image = $_FILES["image"];
                $errors = ImageUtils::validate_image($image); // validation de l'image
                if (count($errors) == 0) // si le fichier est valide on peut proceder a son upload
                    $this->upload_image($item, $image, $search_criteria);
            }

            if (isset($_POST["move"])) { // si on a clique sur la fleche de gauche
                $this->swap_priority($item->get_id(), $_POST["move"]);
            } else if (isset($_POST["delete"])) {
                $this->delete_image($item, $_POST["delete"]);
            }

            if (count($_POST) != 0) // si on requete post -> redirection vers GET sinon on affiche la page avec les erreurs
                $this->redirect("item", "manage_images", "" . $item->get_id(), $search_criteria);
            (new View("manage_images"))->show(["user" => $user, "item" => $item, "errors" => $errors, "search_criteria" => $search_criteria]);
        } else {
            $this->redirect("item"); // si l'utilisateur n'a pas le droit d'acceder a cette page, redirection vers index d'item, donc browse
        }
    }


    private function upload_image(Item $item, array $image, string $search_criteria): void
    {
        //verifier si un dossier existe dans uploads en regardant si l'item en question possede une image
        $this->create_dir_if_not_exists($item);
        // redimensionner le fichier en deux images une en 1080p et une autre en 360px 
        $formatted_images = $this->create_new_images($image);
        // sauvegarde des nouvelles images dans le repertoire uploads
        $new_image_path = $this->make_image_path($image, $item->get_id());
        $this->save_new_images($formatted_images, $new_image_path);
        // on l'insere dans la db
        $item->upload_image($new_image_path);
        // redirection PRG
        $this->redirect("item", "manage_images", "" . $item->get_id(), $search_criteria);
    }

    private function create_dir_if_not_exists(Item $item): void
    {
        if ($item->get_number_of_pictures() == 0)
            mkdir("uploads/items/" . $item->get_id());
    }

    private function create_new_images(array $image): array
    {
        $new_images["full_image"] = ImageUtils::scale_image($image, Configuration::get("picture_resolution"));
        $new_images["thumbnail"] = ImageUtils::scale_image($image, Configuration::get("thumbnail_resolution"));
        return $new_images;
    }

    private function save_new_images(array $images, string $path): void
    {
        ImageUtils::save_image_file($images["full_image"], $path);
        ImageUtils::save_image_file($images["thumbnail"], ImageUtils::get_thumbnail_path($path));
    }

    private function make_image_path(array $image, $item_id): string
    {
        $file_name = str_replace(" ", "", pathinfo($image["name"], PATHINFO_FILENAME));
        $unique_file_name = uniqid($file_name); // creation d'un nom unique pour eviter d'avoir deux images avec le meme path dans la DB
        $picture_path = "uploads/items/$item_id/$unique_file_name.jpg";
        return $picture_path;
    }

    private function swap_priority(int $item_id, int $post_value): void
    {
        $increment = $post_value < 0 ? -1 : 1;
        $old_priority = $post_value * $increment;
        $new_priority = $post_value * $increment + $increment;
        Picture::swap_priorities($item_id, $old_priority, $new_priority);
    }

    private function delete_image(Item $item, int $priority): void
    {
        $image_path = $item->get_path($priority);
        $item->delete_picture($priority);
        ImageUtils::delete_image_files($image_path);
        $this->check_for_empty_picture_dir($item);
    }

    private function check_for_empty_picture_dir(Item $item): void
    {
        $item = Item::get_item_by_id($item->get_id());
        // si le dossier est vide, donc nb_pictures == 0, on le supprime
        if ($item->get_number_of_pictures() == 0)
            rmdir("uploads/items/" . $item->get_id());
    }

    public  function sales(): void
    {
        $user = $this->get_user_or_redirect();
        $action = "sales";
        $salesItem = Item::get_user_sold_item($user->get_id());
        $info = Item::cal_total_avg_nbrOfSales($salesItem);
        $loyer_buyer = Item::find_loyer_bider($salesItem, "buyer");
        $nom = $this::concate_names($loyer_buyer);

        (new View("item_closed"))->show(["salesItem" => $salesItem, "info" => $info, "loyer_buyer" => $loyer_buyer, "nom" => $nom, 'user' => $user, 'action' => $action]);
    }

    public function purchases(): void
    {
        $user = $this->get_user_or_redirect();
        $action = "purchases";
        $salesItem = Item::get_purchases_items($user);
        $info = Item::cal_total_avg_nbrOfSales($salesItem);
        $top_seller = Item::find_loyer_bider($salesItem, "pseudo");
        $nom = $this::concate_names($top_seller);

        (new View("item_closed"))->show(["salesItem" => $salesItem, 'info' => $info, 'loyer_buyer' => $top_seller, "nom" => $nom, 'user' => $user, 'action' => $action]);
    }

    private function concate_names(array $loyer_buyer): string
    {
        $nom = "";
        foreach ($loyer_buyer as $buyer) {
            $nom = ($nom == "" ? $buyer : $nom . " et " . $buyer);
        }
        return $nom;
    }



    // SERVICES

    /**
     * Service qui vérifie si un utilisateur est le highest bidder
     */
    public function is_highest_bidder_service(): void
    {
        $user = $this->get_user_or_false();
        if ($user) {
            $item_id = "";
            $user_id = "";

            // recherche des paramètres
            if (isset($_GET["param1"]) && $_GET["param1"] != "") {
                $item_id = $_GET["param1"];
            }
            if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                $user_id = $_GET["param2"];
            }

            if ($item_id == "" || $user_id == "") { // si item ou user toujours pas valide
                http_response_code(400); // Paramètres reçu non valides
            } else { // on fait la query
                $item = Item::get_item_by_id($item_id);
                echo $item->is_highest_bidder($user_id);
            }
        } else {
            http_response_code(401); // erreur "Unauthorized"
        }
    }


    /**
     * Service qui verifie si l'utilisateur connecté est le bidder d'un item
     * On cherche l'item, puis on regarde parmi ses bids si il en existe un tel que
     * owner = id_user_connecté
     */
    public function has_bidder_service(): void
    {
        if ($this->get_user_or_false()) {
            $item_id = "";
            $user_id = "";
            if (isset($_GET["param1"]) && $_GET["param1"] != "") {
                $item_id = $_GET["param1"];
            }
            if (isset($_GET["param2"]) && $_GET["param2"] != "") {
                $user_id = $_GET["param2"];
            }

            $item = Item::get_item_by_id($item_id);
            echo $item->has_bidder($user_id);
        } else {
            http_response_code(401); // erreur "Unauthorized"
        }
        //echo false;
    }

    public function is_open_item_service(): void
    {
        // doit s'afficher en mode invite donc on ne verifie pas si un user est connecte
        $item_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $item_id = $_GET["param1"];
        }
        $item = Item::get_item_by_id($item_id);
        echo $item->is_open_item();
    }


    public function get_item_period_service(): void
    {
        // pas besoin de securite ici, car on en a besoin en mode invite
        $item_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            $item_id = $_GET["param1"];
        }
        $item = Item::get_item_by_id($item_id);
        echo $item->get_period();
    }


    /**
     * Service qui va encoder le critère de recherche
     */
    public function get_encoded_data_service(): void
    {
        if (isset($_GET["param1"]) && $_GET["param1"] != "") {
            echo EncoderDecoder::url_safe_encode($_GET["param1"]);
        }
    }

    /**
     * Service qui recupere les constantes d'un item depuis le fichier dev.ini
     */
    public function get_item_rules_service(): void
    {
        if ($this->get_user_or_false()) {
            $title_min_length = Configuration::get("title_min_length");
            $title_max_length = Configuration::get("title_max_length");
            $description_min_length = Configuration::get("description_min_lenght");
            $description_max_length = Configuration::get("description_max_lenght");
            $duration_min = Configuration::get("duration_min");
            $duration_max = Configuration::get("duration_max");
            $duration_required = Configuration::get("duration_required");
            $duration_rules = Configuration::get("duration_rules");
            $unicity_title = Configuration::get("unicity_title");
            $title_required = Configuration::get("title_required");
            $length_title = Configuration::get("lenght_title");
            $description_length = Configuration::get("description_lenght");
            $duration_days = Configuration::get("duration_days");
            $starting_bid = Configuration::get("starting_bid");
            $buy_now_positif = Configuration::get("buy_now_positif");
            $buy_now_sup_starting_bid = Configuration::get("buy_now_sup_starting_bid");
            $both = Configuration::get("both");
            $option_required = Configuration::get("option_required");

            $json = json_encode([
                "title_min_length" => $title_min_length,
                "title_max_length" => $title_max_length,
                "description_min_length" => $description_min_length,
                "description_max_length" => $description_max_length,
                "duration_required" => $duration_required,
                "duration_rules" => $duration_rules,
                "duration_min" => $duration_min,
                "duration_max" => $duration_max,
                "unicity_title" => $unicity_title,
                "title_required" => $title_required,
                "length_title" => $length_title,
                "description_length" => $description_length,
                "duration_days" => $duration_days,
                "starting_bid" => $starting_bid,
                "buy_now_positif" => $buy_now_positif,
                "buy_now_sup_starting_bid" => $buy_now_sup_starting_bid,
                "both" => $both,
                "option_required" => $option_required
            ]);

            echo $json;
        } else {
            http_response_code(401); // Si utilisateur non connecté "Unauthorized
        }
    }

    /**
     * Service qui va verifier l'unicite du titre d'un item pour l'utilisateur connecte
     */
    function check_title_unicity_service(): void
    {
        $user = $this->get_user_or_false();
        if ($user) {
            if ((isset($_POST["title"]) && $_POST["title"] != "") && (isset($_POST["id"]) && $_POST["id"] != "")) {
                $title = $_POST["title"];
                // si le titre a des espaces sous forme de '+' on les remets en espaces
                $item_id = $_POST["id"];
                $is_unique = Item::unicite_title_for_owner($user->get_id(), $title, $item_id);
                echo json_encode($is_unique);
            }
            else{
                http_response_code(400); // pas de paramètres donc bad request
            }
        } else {
            http_response_code(401); // Unauthorized
        }
    }

    /**
     * Service qui va swap la priorite d'une image vers une nouvelle priorite
     */
    function swap_image_priority_service(): void
    {
        $user = $this->get_user_or_false();
        if ($user) { // si le user existe
            if (isset($_POST["item_id"]) && is_numeric($_POST["item_id"])) { // si l'id entre existe et est numerique
                $item = Item::get_item_by_id($_POST["item_id"]); // recherche de l'item
                if ($item && $item->get_owner() === $user->get_id()) { // si l'item existe et appartient a l'owner
                    $order = $_POST["order"];
                    for ($i = 0; $i < count($order); $i++) {
                        // chaque entree dans le tableau represente la priorite d'une image
                        // pour chaque image de priorite $order[$i], on lui donne une priorite de -1 * ($i+1)
                        // par exemple, image de p3, aura une priorite de -1 si elle est en debut de tableau
                        // puis une fois que tous les element ont une nouvelle priorite, on remet les priorite en positif
                        $new_priority = -1 * ($i + 1);
                        Picture::update_priority($item->get_id(), $order[$i], $new_priority);
                    }
                    Picture::update_to_pos_priorities($item->get_id());
                }
                else{
                    http_response_code(403); // item appartient pas a l'owner, forbidden
                }
            }
            else {
                http_response_code(400); // bad request si pas de params
            }
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "User is not connected"]);
        }
    }


    /**
     * Service qui retourne un tableau json des images d'un item
     */
    public function get_item_pictures_service(): void
    {
        // pas besoin de check si un utilisateur est connecté vu qu'un invité peut aussi voir les images 
        if (isset($_GET["param1"]) && is_numeric($_GET["param1"])) { // si l'id entre existe et est numerique
            $item = Item::get_item_by_id($_GET["param1"]); // recherche de l'item
            echo json_encode($item->get_pictures());
        }
        else{
            http_response_code(400); //Bad request
        }
    }
}
