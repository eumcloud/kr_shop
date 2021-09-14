<?php
    /**
    * ��ũ�����̽��� ���Ͽ� ������ ������ ��� ��ǰ���� �Ϸ�� ��ũ�����̽��� �ǽð� ���� ������ �ؾ� �մϴ�.
    *
    * ��, ����� ��ǻ�Ϳ� LPINFO ��Ű���� �ִٸ� ��ũ�����̽��� ���� ������ �ؾ� �ϸ� �ش� ������ DB�� �����ؾ� �մϴ�.
    *
    * �����ڵ忡��
    *
    *  1. ��ũ�����̽��� �ǽð� ���� ����.
    *  2. ��ũ�����̽� ���� ����
    *
    * ������ ���ԵǾ� �ֽ��ϴ�.
    *
    * �����ڵ带 �ڻ��� �ý��ۿ� �°� �����Ͽ� ��ǰ���� �Ϸ� �������� �����Ͽ� �ֽø� �ǰڽ��ϴ�.
    **/

    /**
    * ����ڰ� �ٸ� ������ ���� ��ǰ�� ��ٱ��Ͽ� ��� ���Ÿ� �� �� �ֽ��ϴ�.
    * �̿Ͱ��� ���� ��ǰ�� ��ũ�����̽��� ������ ������ �� ��ǰ�� ������ �����Ͽ� ������ �˴ϴ�.
    * �̸� ó���ϱ� ���ϸ� ���� for loop�� ����˴ϴ�.
    * ������ ��ٱ��ϸ� ó���ϴ� �κ��� �ִٸ� for loop ������ ������ ��
    *
    *     $p_cd_ar[]   = $product_code;
    *     $it_cnt_ar[] = $item_count;
    *     $c_cd_ar[]   = $category_code;
    *     $sales_ar[]  = $price;
    *     $p_nm_ar[]   = $product_name;
    *
    * �κ��� ��ٱ��� ó�� �κп� �־��ּŵ� �ǰڽ��ϴ�.
    * $product_code�� ��ǰ�ڵ� �Դϴ�.
    * $item_cout�� ��ǰ�� ���� �Դϴ�. ��, $product_code�� A�� ��ǰ�� 2�� �����ߴٸ� 2�� �ǰڽ��ϴ�.
    * $category_code�� ��ǰ�� ī�װ� �ڵ尪 �Դϴ�. ���� ī�װ� �ڵ尡 ���ٸ� null�� ó���� �ֽñ� �ٶ��ϴ�.
    * $price���� ��ǰ�� ������ �־��ֽø� �ǰڽ��ϴ�. ������ ��ǰ�� unitprice�� �ƴ� item_count*unitprice�� ���Դϴ�.
    * ��, ��ǰ A�� 2�� �����߰�, 1���� ������ 1000���̶�� $price�� 2000���� �ǰڽ��ϴ�. ���� 2000�� �־��ֽø� �ǰڽ��ϴ�.
    * �ǸŰ����� ���̳ʽ� �� ��� 0 ������ �����ϵ��� ����ó�� ���ּž� �մϴ�.
    * $product_name �� ��ǰ�̸��Դϴ�.
    * ĳ���ͼ��� UTF-8�ϰ�� EUC-KR�� ��ȯ�Ͻø� �ǰڽ��ϴ�.
    * �̷��� ������ array�� ���� ���� ���� ���� �κп��� ���˴ϴ�.
    * �ڼ��� ������ http://setup.linkprice.com �� �����Ͽ� �ֽñ� �ٶ��ϴ�.
    *
    **/

    for ($i=0; $i<(��ٱ��� ��ǰ count); $i++)
    {
        $p_cd_ar[]   = $product_code;
        $it_cnt_ar[] = $item_count;
        $c_cd_ar[]   = $category_code;
        $sales_ar[]  = $price;
        $p_nm_ar[]   = $product_name;
    }

    if (isset($LPINFO))
    {
        /**
        * a_id   : LPINFO ��Ű�� �Դϴ�. �����ڵ� �״�� ����Ͻø� �ǰڽ��ϴ�.
        * m_id   : �ڻ��� ID ���Դϴ�. (��õƮ ���̵�) �����ڵ� �״�� ����Ͻø� �ǰڽ��ϴ�.
        * mbr_id : ��ǰ���Ÿ� �� ����� ���� �Դϴ�. '�����ID(�̸�)' ���·� �־��ֽø� �ǰڽ��ϴ�.
        *          $id, $name �� �ڻ��� �ý��ۿ� �´� ������ ������ �ֽø� �ǰڽ��ϴ�.
        * o_cd   : ��ǰ���� �ֹ���ȣ �Դϴ�. $order_code�� �ڻ��� �ֹ���ȣ ���� �־��ֽø� �ǰڽ��ϴ�.
        * ���� �Ϸ��������� SSL�� ����ϽŴٸ� ���� URL��
        * https://service.linkprice.com/lppurchase.php �� ������ �ֽñ� �ٶ��ϴ�.
        **/

        $linkprice_url = "http://service.linkprice.com/lppurchase.php";     // �����Ͻø� �ȵ˴ϴ�.
        $linkprice_url.= "?a_id=".$LPINFO;                                  // �����Ͻø� �ȵ˴ϴ�.
        $linkprice_url.= "&m_id=clickbuy";                           // �����Ͻø� �ȵ˴ϴ�.
        $linkprice_url.= "&mbr_id=".$id."(".$name.")";                      // $id = ����� ID��, $name = ����� �̸���, ���� �� �� ���� ���� �ִٸ� �����ϴ� ������ �־��ֽñ� �ٶ��ϴ�.
        $linkprice_url.= "&o_cd=".$order_code;                              // $order_code = �ֹ���ȣ�� �Դϴ�.
        $linkprice_url.= "&p_cd=".implode("||", $p_cd_ar);                  // �����Ͻø� �ȵ˴ϴ�. $p_cd_ar�� ���� ��ٱ��� ó�� �κп��� ������ ���Դϴ�.
        $linkprice_url.= "&it_cnt=".implode("||", $it_cnt_ar);              // �����Ͻø� �ȵ˴ϴ�. $it_cnt_ar�� ���� ��ٱ��� ó�� �κп��� ������ ���Դϴ�.
        $linkprice_url.= "&sales=".implode("||", $sales_ar);                // �����Ͻø� �ȵ˴ϴ�. $sales_ar�� ���� ��ٱ��� ó�� �κп��� ������ ���Դϴ�.
        $linkprice_url.= "&c_cd=".implode("||", $c_cd_ar);                  // �����Ͻø� �ȵ˴ϴ�. $c_cd_ar�� ���� ��ٱ��� ó�� �κп��� ������ ���Դϴ�.
        $linkprice_url.= "&p_nm=".implode("||", $p_nm_ar);                  // �����Ͻø� �ȵ˴ϴ�. $p_nm_ar�� ���� ��ٱ��� ó�� �κп��� ������ ���Դϴ�.

        /** ��ũ�����̽� data ��ȣȭ ó��
        * lpbase64.php ������ ���� ������ �����ϼ��� ���
        *
        * require_once "lpbase64.php"; �κ��� �״�� ����Ͻø� �ǰڽ��ϴ�.
        *
        * ������ ����Ǿ��� ��� ��Ȯ�� ��θ� �־��ּž� �մϴ�.
        *
        * code, pad ���� ���� �����Ͻø� �ȵ˴ϴ�. �ҽ��ڵ��� ���� �״�� ����� �ֽñ� �ٶ��ϴ�.
        **/

        require_once "lpbase64.php";

        $code = "00036";   // �����Ͻø� �ȵ˴ϴ�.
        $pad  = "RE.F8rtI29yLuP*o3CVzSBgWw5edijZYKOv0pc4xqkMAUlD6bsHTGmN1Xfn7JahQ";    // �����Ͻø� �ȵ˴ϴ�.
        $linkprice_url = lp_url_trt($linkprice_url, $code, $pad);
        $linkprice_tag = "<script src=\"$linkprice_url\"></script>";

        /** ��ũ�����̽� ���� DB�� ����
        * ��ũ�����̽��� ���Ͽ� �߻��� ������ DB�� ���� ������ ���ּž� �մϴ�.
        * ���� �߻��� �ǽð����� ������ ������ ������ �� �� �ֱ� ������ ũ�ν� üũ�� �ؾ� �մϴ�.
        * ���� ������ ������ �ּž� �ϸ�, ��ũ�����̽����� �� �����͸� �Ϸ翡 �ѹ��� �޾Ƽ� ������ ������ ����ó�� �մϴ�.
        * �̰����� ������ DB�� �����մϴ�.
        * DB�� �����ϱ� ���ؼ� ��ũ�����̽� ���̺��� �����ϼž� �մϴ�. Ȥ�� �ڻ��� ���̺� field�� �߰��� �ּŵ� �˴ϴ�.
        * �׸��� ���� ����� LPINFO ��Ű���� �� �Բ� ������ �ּž� �մϴ�. �̴� varchar2(100)���� ���ּž� �մϴ�.
        *
        * ���̺� ���� ����,
        *
        *     create table TLINKPRICE (
        *         LPINFO    varchar2(100),
        *            YYYYMMDD    char(8),
        *            HHMISS        char(6),
        *            ORDER_CODE    varchar2(100),       // �ڻ��� �ֹ���ȣ(o_cd) field �� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            PRODUCT_CODE    varchar2(100),     // �ڻ��� ��ǰ�ڵ�(p_cd) field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            ITEM_COUNT        number(5),       // �ڻ��� ���ż�(it_cnt) field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            PRICE    number(8),                // �ڻ��� ��ǰ����(sales) field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            PRODUCT_NAME varchar2(100),        // �ڻ��� ��ǰ�̸�(p_nm) field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            CATEGORY_CODE    varchar2(100),    // �ڻ��� ī�װ��ڵ�(c_cd) field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            ID    varchar2(10),                // �ڻ��� �����ID field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            NAME    varchar2(10),              // �ڻ��� ����� �̸� field�� ���� ó���� �ֽñ� �ٶ��ϴ�.
        *            REMOTE_ADDR    varchar2(100)       // ��ǰ�� ������ ������� IP�� field�� ������ �ֽñ� �ٶ��ϴ�.
        *        )
        *
        * data insert ����,
        *
        *        $ymd = date("Ymd");
        *        $his = date("His");
        *
        *        for($i=0; $i<count($p_cd_ar); $i++)
        *        (
        *            $str = "
        *                insert into tlinkprice
        *                (
        *                    lpinfo, yyyymmdd, hhmiss,
        *                    order_code, product_code, item_count, price, product_name, category_code,
        *                    id, name, remote_addr
        *                )
        *                values
        *                (
        *                    '$LPINFO', '$ymd', '$his',
        *                    '$order_code', '$p_cd_ar[$i]', $it_cnt_ar[$i], $price[$i], '$p_nm_ar[$i]', '$c_cd_ar[$i]',
        *                    '$id', '$name', '�������ּ�'
        *                )
        *            ";
        *            DB execute ����
        *        )
        *    ���� ������ ���� ���̺��� �����Ͻ� �� ���� ���۽ÿ� �ش� �����͸� ���̺� �����Ͻø� �ǰڽ��ϴ�.
        * DB�� ������ �� ������ �ּž� �մϴ�.
        **/
    }
?>
<html>
<body>
<!-- ��ũ�����̽��� ���� ���� -->
<?=$linkprice_tag?>
</body>
</html>