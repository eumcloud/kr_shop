<?php 
class MessageTag
{
	var $USER_ID                      = "0001" ;  // ���� ID
	var $USER_NAME                    = "0002" ;  // ���� �̸�
	var $ITEM_CODE                    = "0003" ;  // ��ǰ �ڵ�
	var $ITEM_NAME                    = "0004" ;  // ��ǰ �̸�
	var $USER_IP                      = "0005" ;  // ���� IP
	var $USER_EMAIL                   = "0006" ;  // ���� Email
	var $MOBILE_NUMBER                = "0007" ;  // �޴��� ��ȣ
	var $PIN_NUMBER                   = "0008" ;  // �� ��ȣ
	var $PASSWORD                     = "0009" ;  // �н�����
	var $BUSINESS_NUMBER              = "0010" ;  // ����� ��� ��ȣ
	var $DEAL_DATE                    = "0011" ;  // �ŷ� �Ͻ�
	var $DEAL_AMOUNT                  = "0012" ;  // �ŷ� �ݾ�(���ް���)
	var $VAT                          = "0013" ;  // �ΰ���
	var $SERVICE_CHARGE               = "0014" ;  // �����
	var $USING_TYPE                   = "0015" ;  // �ŷ��� ����
	var $DEAL_TYPE                    = "0016" ;  // �ŷ� ����
	var $IDENTIFIER                   = "0017" ;  // �ź� Ȯ��
	var $CASH_ID                      = "0018" ;  // ĳ�� ID
	var $CASH_PASSWORD                = "0019" ;  // ĳ�� �н�����
	var $PIN_PASSWORD                 = "0020" ;  // �� �н�����
	var $MALL_USER_ID                 = "0021" ;  // ������ ���� ID
	var $AFFILIATER_REGISTER_ID       = "0022" ;  // ���޻� ��� ���̵�
	var $ITEM_KIND                    = "0023" ;  // ��ǰ ����
	var $NOTIFY_TYPE                  = "0024" ;  // ���� ����
	var $ORDER_ID                     = "0025" ;  // ���� ��û�� �ֹ� ��ȣ
	var $TERMINAL_ID                  = "0026" ;  // �͹̳� ID
	var $CURRENCY                     = "0030" ;  // ��ȭ ����	          
	var $QUOTA                        = "0031" ;  // �Һ� ���� �� 	    
	var $EXPIRE_DATE                  = "0032" ;  // ��ȿ����	        
	var $CVC2                         = "0033" ;  // CVC2	            
	var $CARD_COMPANY_CODE            = "0034" ;  // ī��� �ڵ� 	    
	var $CERT_TYPE                    = "0035" ;  // ISP/MIP ����	    
	var $INTEREST_TYPE                = "0036" ;  // ������ �Һ� ����	
	var $MIX_TYPE                     = "0037" ;  // ���հ��� ����	    
	var $RECEIVER_NAME                = "0038" ;  // ������ ����	    
	var $RECEIVER_ADDRESS             = "0039" ;  // ������ �����      
	var $MPI_CAVV                     = "0040" ;  // MPI CAVV           
	var $MPI_XID                      = "0041" ;  // MPI X-ID           
	var $MPI_ECI                      = "0042" ;  // MPI EC-I           
	var $SESSION_KEY                  = "0043" ;  // SessionKey         
	var $ENCRYPT_DATA                 = "0044" ;  // Encrypted Data     
	var $IC_DATA_TYPE                 = "0045" ;  // IC DATA ����       
	var $IC_DATA                      = "0046" ;  // IC DATA            
	var $SIGN_TYPE                    = "0047" ;  // ��������           
	var $SIGN_DATA                    = "0048" ;  // Sign DATA          
	var $ANI                          = "0049" ;  // ANI
	var $DNIS                         = "0050" ;  // DNIS
	var $WIRE_NUMBER                  = "0051" ;  // ������ȣ
	var $AGREE_MONTHS                 = "0052" ;  // ���ǱⰣ
	var $SEARCH_START_DATE            = "0053" ;  // �˻� ��������
	var $SEARCH_END_DATE              = "0054" ;  // �˻� ������
	var $FILE_NAME                    = "0055" ;  // ���ϸ�
	var $FILE_SIZE                    = "0056" ;  // ���� ũ��
	var $FILE_DATA                    = "0057" ;  // ���� ����Ÿ
	var $FILE_SEQ                     = "0058" ;  // ���� Sequence

	var $MOBILE_COMPANY_CODE          = "0059" ;  // �̵���Ż� �ڵ�
	var $RESPONSE_RETURN_URL          = "0060" ;  // �������� ���� URL
	var $RESPONSE_FAIL_URL            = "0061" ;  // �������� ����URL

	var $BANK_ID                      = "0062" ;  // ������̵�
	var $ACCOUNT_NAME                 = "0063" ;  // ���¸�
	var $COMPANY_NAME                 = "0064" ;  // �̿�����
	var $REFUND_FLAG                  = "0065" ;  // ȯ�� ���а�
	var $TRANSFER_FLAG                = "0066" ;  // �ŷ� ���а�
	var $FEE                          = "0067" ;  // ������
	var $CPCODE                       = "0068" ;  // CPCODE
	var $SOCIAL_NUMBER                = "0069" ;  // �ֹι�ȣ
	var $OPCODE                       = "0070" ;  // OPCODE
	var $CRYPTO_SOCIAL_NUMBER         = "0071" ;  // ��ȣȭ��
	var $CRYPTO_ANI                   = "0072" ;  // ��ȣȭ��
	var $CRYPTO_CASH_ID               = "0073" ;  // ��ȣȭ��
	var $AFFILIATER_CODE              = "0074" ;  // ���޻� �ڵ�
	var $EMAIL_TEMPLATE_CODE          = "0075" ;  // ���� ���ø� �ڵ�
	var $CALL_CENTER_NEMBER			  = "0076" ;  // ������ �ݼ��� ��ȭ��ȣ 

	var $BANK_CUSTOMER_CODE			  = "0077" ;  // ������ ���� ����ڵ�
	var $STATUS_CODE				  = "0078" ;  // �����ڵ�
	var $MULTI_BILL_ACCOUNT_CODE	  = "0079" ;  // ������� �ٰ��� �����ڵ�
	var $ACCOUNT_NUMBER				  = "0080" ;  // ������� ��ȣ
	var $ACCOUNT_ID					  = "0081" ;  // ������� ������ȣ
	var $REQUIRE_TYPE				  = "0082" ;  // �����ʼ����� 

	var $ADSL_ID					  = "0083" ;  // �ް��н� ID
	var $ADSL_BALANCE				  = "0084" ;  // ADSL �ܾ�
	var $ARS_BALANCE				  = "0085" ;  // ARS �ܾ�
	var $ARS_AUTH_AMOUNT			  = "0086" ;  // ARS ���� �ݾ�
	var $ADSL_AUTH_AMOUNT			  = "0087" ;  // ADSL ���� �ݾ�
	var $MAX_DATE					  = "0088" ;  // MAX DATE
	var $NOW_DATE					  = "0089" ;  // NOW DATE
	var $RANDOM_CERT_NUMBER           = "0090" ;  // ���� ���� ��ȣ
	var $SERVICE_TYPE                 = "0091" ;  // ����Ÿ��
	var $SERVICE_TYPE_DETAIL          = "0092" ;  // ����Ÿ�� ��

	var $BANK_CODE                    = "0093" ;  // �����ڵ�
	var $PROCESS_DATE                 = "0094" ;  // ó������
	var $CPCODE_PASSWORD              = "0095" ;  // CPCODE �н�����
	var $PROTOCOL_NUMBER              = "0096" ;  // ������ȣ
	var $PRE_PROTOCOL_NUMBER          = "0097" ;  // ���� ������ȣ
	var $TRANSFER_DATE                = "0098" ;  // ��������
	var $TRANSFER_TIME                = "0099" ;  // ���۽ð�
	var $SERVICE_KIND_CODE            = "0100" ;  // ���� ���� �ڵ�
	var $SERVICE_TRANSACTION_ID       = "0101" ;  // ���� TRANSACTION ID
	var $IDENTIFIER_TYPE              = "0102" ;  // �ź�Ȯ�� �� ���� �ڵ� (01: �ֹι�ȣ, 02:�޴���, 03: ���ݿ�����ī���ȣ, 04:����ڹ�ȣ)
	var $PRE_TRANSFER_DATE            = "0103" ;  // ���� ��������
	var $AGENT_NAME                   = "0104" ;  // ������ ��

	var $INPUT_BANK_CODE              = "0105" ;  // �Ա� �����ڵ� 
	var $INPUT_ACCOUNT_NUMBER         = "0106" ;  // �Ա� ���¹�ȣ
	var $INPUT_ACCOUNT_NAME           = "0107" ;  // �Ա� ���� �����ָ�
	var $INPUT_ACCOUNT_PASSWORD       = "0108" ;  // �Ա� ���� ��й�ȣ
	var $OUTPUT_BANK_CODE             = "0109" ;  // ��� �����ڵ� 
	var $OUTPUT_ACCOUNT_NUMBER        = "0110" ;  // ��� ���¹�ȣ
	var $OUTPUT_ACCOUNT_NAME          = "0111" ;  // ��� ���� �����ָ�
	var $OUTPUT_ACCOUNT_PASSWORD      = "0112" ;  // ��� ���� ��й�ȣ
	var $OUTPUT_ACCOUNT_SOCIAL_NUMBER = "0113" ;  // ��� ���� ���½Ǹ���ȣ
	var $INPUT_ACCOUNT_PRINT          = "0114" ;  // �Ա� ���� ����
	var $OUTPUT_ACCOUNT_PRINT         = "0115" ;  // ��� ���� ����

	var $TRANSACTION_ID               = "1001" ;  // �ŷ� ��ȣ
	var $RESPONSE_CODE                = "1002" ;  // ���� �ڵ�
	var $RESPONSE_MESSAGE             = "1003" ;  // ���� �޽���
	var $AUTH_NUMBER                  = "1004" ;  // ���� ��ȣ
	var $AUTH_DATE                    = "1005" ;  // ���� �Ͻ�
	var $BALANCE                      = "1006" ;  // �ܾ�
	var $AUTH_AMOUNT                  = "1007" ;  // ���� �ݾ�
	var $CANCEL_DATE                  = "1008" ;  // ��� �Ͻ�
	var $DETAIL_RESPONSE_CODE         = "1009" ;  // �� ���� �ڵ�
	var $DETAIL_RESPONSE_MESSAGE      = "1010" ;  // �� ���� �޽���
	var $REQUEST_DATE                 = "1011" ;  // �ŷ� ��û ����
	var $PRE_TRANSACTION_ID           = "1012" ;  // ���� �ŷ� ��ȣ
	var $AFFILIATER_RESPONSE_CODE     = "1013" ;  // ���޻� �����ڵ� 
	var $EXPIRATION_DATE              = "1014" ;  // ��ȿ �Ⱓ
	var $CARD_TYPE                    = "1015" ;  // ī�� Ÿ��(���丮 ��ǰ�� : ������, ������, slip ��, online)
	var $ISSUE_DATE                   = "1016" ;  // ���� ����
	var $CERT_NUMBER                  = "1017" ;  // ���� ��ȣ
	var $USER_KEY                     = "1018" ;  // ����� Ű
	var $AFFILIATER_TRANSACTION_ID    = "1019" ;  // ���޻� �ŷ� ��ȣ
	var $AGENT_NUMBER                 = "1020" ;  // ������ ��ȣ
	var $ISSUE_COMPANY_CODE           = "1021" ;  // ī��߱޻��ڵ�
	var $BUY_COMPANY_CODE             = "1022" ;  // ī����Ի��ڵ�

	var $RESULT                       = "1023" ;  // result
	var $ERROR_CODE                   = "1024" ;  // errorCode
	var $CALLER_ID                    = "1025" ;  // caller_id
	var $SERVICE_ID                   = "1026" ;  // service_id
	var $ID_BALANCE                   = "1027" ;  // ���̵� �ܾ�

	var $TRANSFER_COUNT               = "1028" ;  // ��ü�Ǽ�
	var $TRANSFER_AMOUNT              = "1029" ;  // ��ü�ݾ�
	var $REFUND_COUNT                 = "1030" ;  // ������ü�Ǽ�
	var $REFUND_AMOUNT                = "1031" ;  // ������ü�ݾ�

	var $CANCEL_AMOUNT                = "1033" ;  // ��ұݾ�

	var $RESERVED01                   = "9001" ;  // �ӽ� �ʵ�
	var $RESERVED02                   = "9002" ;  // �ӽ� �ʵ�
	var $RESERVED03                   = "9003" ;  // �ӽ� �ʵ�	
}
?>