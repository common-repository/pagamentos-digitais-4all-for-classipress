<?php

  class Gateway_4all
  {
    function __construct($gatewaySettings) {
      $this->merchantKey = $gatewaySettings["merchantKey"];
      $this->environment = $gatewaySettings["environment"];
    }

    function request_4all($url, $body) {

      $args = array(
        'body' => json_encode($body),
        'timeout' => '30',
        'redirection' => '5',
        'httpversion' => '1.1',
        'blocking' => true,
        'headers' => array(
          "Cache-Control" => "no-cache",
          "Content-Type" => "application/json",
        ),
        'cookies' => array()
      );

      $response = wp_remote_post( $this->environment . $url, $args );

      if (is_wp_error($response)) {
        $error = array("error" => ["code" => "1024536985", "message" => "Unable to connect with 4all."]);
        return json_encode($error); 
      } else {
        return json_decode($response['body'], true);
      }
    }

    public function paymentFlow_4all($metaData) {
      $this->cardData = $metaData["cardData"];
      $responseRequestVaultKey = $this->requestVaultKey_4all();
      if ($responseRequestVaultKey["error"]) {
        return $responseRequestVaultKey;
      }
      $responsePrepareCard = $this->prepareCard_4all($responseRequestVaultKey["accessKey"]);
      if ($responsePrepareCard["error"]) {
        return $responsePrepareCard;
      }
      $responseCreateTransaction = $this->createTransaction_4all($responsePrepareCard, $metaData);
      return $responseCreateTransaction;
    }

    function requestVaultKey_4all()
    {
      try {
        $body = array("merchantKey" => $this->merchantKey);
        $response = $this->request_4all('requestVaultKey', $body);
        return $response;
      } catch (HttpException $ex) {
        $error = array("error" => ["code" => "2154893201", "message" => "Could not validate card."]);
        return $error;
      }
    }
    
    function prepareCard_4all($accessKey)
    {
      try {
        $this->cardData["expirationDate"] = str_replace("/", "", $this->cardData["expirationDate"]);

        $body = array(
          "accessKey" => $accessKey,
          "cardData" => 
            [
              "type" => 1, 
              "cardholderName" => $this->cardData["cardholderName"],
              "cardNumber" => $this->cardData["cardNumber"],
              "expirationDate" => $this->cardData["expirationDate"],
              "securityCode" => $this->cardData["securityCode"]
            ]
          );
        $response = $this->request_4all('prepareCard', $body);
        return $response;
      } catch (HttpException $ex) {
        $error = array("error" => ["code" => "4520198237", "message" => "Could not prepare the card."]);
        return $error;  
      }
    }

    function createTransaction_4all($cardCredential, $metaData)
    {
      try {
        $body = array(
          "merchantKey" => $this->merchantKey,
          "amount" => $metaData["total"],
          "metaId" => $metaData["metaId"],
          "overwriteMetaId" => true,
          "paymentMethod" => 
            [[
              "cardNonce" => $cardCredential["cardNonce"],
              "cardBrandId" => $cardCredential["brandId"],
              "amount" => $metaData["total"],
              "installment" => (int)$metaData["installment"]
            ]],
          "autoCapture" => true
        );

        if ($metaData["customer"]) {
          $body["customerInfo"] = array(
            "fullName" => $metaData["customer"]["fullName"],
            "cpf" => $metaData["customer"]["cpf"],
            "address" => $metaData["customer"]["address"],
            "city" => $metaData["customer"]["city"],
            "state" => $metaData["customer"]["state"],
            "zipCode" => $metaData["customer"]["zipCode"],
            "phoneNumber" => $metaData["customer"]["phoneNumber"],
            "emailAddress" => $metaData["customer"]["emailAddress"]
          );
        }

        $response = $this->request_4all('createTransaction', $body);
        if ($response["status"] !== 4) {
          if ($response["status"] === 0 || $response["status"] === 2 || $response["status"] === 3) {

            if ($response["status"] === 3) {
              sleep(9);
              $details = $this->getTransactionDetails_4all($response["transactionId"]);

              if (!$details["error"] && $details["status"] == 4) {
                $response["status"] = $details["status"];
                return $response;
              }
            }

            $canceled = false;
            $count = 0;
            while(true)
            {
              $canceled = $this->cancelTransaction_4all($response["transactionId"]);
              $count++;
              if ($count = 3 || $canceled == true) {
                break;
              }
            }
            if ($canceled == true) {
              $response = array("error" => ["code" => "3546982157", "message" => "Could not make payment. (3546982157)"]);
            } else {
              $response = array("error" => ["code" => "4793105861", "message" => "Error."]);
            }
          } else {
            $response = array("error" => ["code" => "3546982158", "message" => "Could not make payment. (3546982158)"]);
          }
        }
        return $response;
      } catch (HttpException $ex) {
        $error = array("error" => ["code" => "3546982159", "message" => "Could not make payment. (3546982159)"]);
        return $error;
      }
    }
    
    function cancelTransaction_4all($id)
    {
      try {
        $body = array(
          "merchantKey" => $this->merchantKey,
          "transactionId"=> $id 
        );
        $response = $this->request_4all('cancelTransaction', $body);
        if ($response["error"]) {
          return false;
        }
        return true;
      } catch (HttpException $ex) {
        return false;
      }
    }

    function getPaymentMethods_4all()
    {
      try {
        $body = array(
          "merchantKey" => $this->merchantKey,
        );
        $response = $this->request_4all('getPaymentMethods', $body);
        return $response;
      } catch (HttpException $ex) {
        $error = array("error" => ["code" => "1876416810", "message" => "Error on try get payment methods."]);
        return $error;
      }
    
    }

    function getTransactionDetails_4all($id)
    {
      try {
        $body = array(
          "merchantKey" => $this->merchantKey,
          "transactionId"=> $id 
        );
        $response = $this->request_4all('getTransactionDetails', $body);
        return $response;
      } catch (HttpException $ex) {
        $error = array("error" => ["code" => "1876435810", "message" => "Error on try get transaction details."]);
        return $error;
      }
    }
  }
  