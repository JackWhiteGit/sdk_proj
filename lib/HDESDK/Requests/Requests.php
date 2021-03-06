<?php


namespace HDESDK\Requests;

use HDESDK\Auth\Auth;

class Requests extends Auth
{
    private $request_url;
    private $request_type;
    private $option_string;
    private $request_header;
    private $request_config;

    public function SetRequest($request_type, $options){

        $this->request_type = self::RequestTypeConfig($request_type,  $options);
        $this->option_string = self::SetOptions($options, $this->request_type);
        $this->request_url = $this->hde_url."/api/v2".$this->request_type['url'].$this->option_string['GET'];
        $this->request_header = self::SetRequestHeader($this->request_type);


        switch ($this->request_type['method']) {
            case 'GET':
            case 'DELETE':
            $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $this->request_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $this->request_type['method'],
                    CURLOPT_HTTPHEADER => $this->request_header
                ));
            $response = curl_exec($curl);
            curl_close($curl);
                break;

            case 'POST':
            case 'PUT':
            $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $this->request_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $this->request_type['method'],
                    CURLOPT_POSTFIELDS => $this->option_string['POST'],
                    CURLOPT_HTTPHEADER => $this->request_header
                ));
            $response = curl_exec($curl);
            curl_close($curl);

                break;

        }
        return $response;
    }

    private function SetOptions($options, $method){

        if(!empty($method['url_part'])){
            foreach ($method['url_part'] as $item) {
                unset($options[$item]);
            }
        }
        $this->option_string['POST'] = '';
        $this->option_string['GET']  = '';
        switch ($method['method']) {
            case 'GET':
                $this->option_string['GET'] = '?';
                foreach ($options as $key=>$value){
                    $this->option_string['GET'] .= $key.'='.urlencode($value).'&';
                }
                $this->option_string['GET'] = trim( $this->option_string['GET'], '&');
                break;

            case 'POST':
            case 'PUT':
                if($method['content_type']=='application/x-www-form-urlencoded'){
                    foreach ($options as $key=>$value){
                        $this->option_string['POST'] .= $key.'='.urlencode($value).'&';
                    }
                    $this->option_string['POST'] = trim( $this->option_string['POST'], '&');
                }elseif ($method['content_type']=='application/json'){
                    $this->option_string['POST'] = json_encode($options);
                }

                break;

        }

        return $this->option_string;
    }

    public function SetRequestHeader($method){

         $header =  array(
            "Authorization: Basic ".$this->auth_token,
            "Cache-Control: no-cache"
        );

        if($method['content_type']!=''){
            array_push($header, "Content-Type: ".$method['content_type'] );
        }
        return $header;
    }


    private function RequestTypeConfig($request_type,  $options){

        $ticket_id = $id = $custom_field_id = $option_id ='';
        if(isset($options['id'])) $id = $options['id'];
        if(isset($options['ticket_id'])) $ticket_id = $options['ticket_id'];
        if(isset($options['custom_field_id'])) $custom_field_id = $options['custom_field_id'];
        if(isset($options['option_id'])) $option_id = $options['option_id'];

        $this->request_config = array(
            'DepartmentListGet' => array(
                'method' => 'GET',
                'url'    => '/departments/',
                'content_type' => '',
                'url_part' => array()
            ),

            'TicketCreate' => array(
                'method' => 'POST',
                'url'    => '/tickets/',
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array()
            ),

            'TicketUpdate' => array(
                'method' => 'PUT',
                'url'    => '/tickets/'.$id,
                'content_type' => 'application/json',
                'url_part' => array('id')
            ),

            'TicketGet' => array(
                'method' => 'GET',
                'url'    => '/tickets/'.$id,
                'content_type' => '',
                'url_part' => array('id')
            ),

            'TicketsGet' => array(
                'method' => 'GET',
                'url'    => '/tickets/',
                'content_type' => '',
                'url_part' => array()
            ),

            'TicketDelete' => array(
                'method' => 'DELETE',
                'url'    => '/tickets/'.$id,
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array('id')
            ),

            'TicketAnswersGet' => array(
                'method' => 'GET',
                'url'    => '/tickets/'.$ticket_id.'/posts/',
                'content_type' => '',
                'url_part' => array('ticket_id')
            ),

            'TicketAnswerSet' => array(
                'method' => 'POST',
                'url'    => '/tickets/'.$ticket_id.'/posts/',
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array('ticket_id')
            ),

            'TicketAnswerUpdate' => array(
                'method' => 'PUT',
                'url'    => '/tickets/'.$ticket_id.'/posts/'.$id,
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array('ticket_id','id')
            ),

            'TicketAnswerDelete' => array(
                'method' => 'DELETE',
                'url'    => '/tickets/'.$ticket_id.'/posts/'.$id,
                'content_type' => '',
                'url_part' => array('ticket_id','id')
            ),

            'CommentsGet' => array(
                'method' => 'GET',
                'url'    => '/tickets/'.$ticket_id.'/comments/',
                'content_type' => '',
                'url_part' => array('ticket_id')
            ),

            'CommentCreate' => array(
                'method' => 'POST',
                'url'    => '/tickets/'.$ticket_id.'/comments/',
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array('ticket_id')
            ),

            'CommentUpdate' => array(
                'method' => 'PUT',
                'url'    => '/tickets/'.$ticket_id.'/comments/'.$id,
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array('ticket_id','id')
            ),

            'CommentDelete' => array(
                'method' => 'DELETE',
                'url'    => '/tickets/'.$ticket_id.'/comments/'.$id,
                'content_type' => '',
                'url_part' => array('ticket_id','id')
            ),

            'UserListGet' => array(
                'method' => 'GET',
                'url'    => '/users/',
                'content_type' => '',
                'url_part' => array()
            ),

            'UserGet' => array(
                'method' => 'GET',
                'url'    => '/users/'.$id,
                'content_type' => '',
                'url_part' => array('id')
            ),

            'UserCreate' => array(
                'method' => 'POST',
                'url'    => '/users/',
                'content_type' => 'application/x-www-form-urlencoded',
                'url_part' => array()
            ),

            'UserUpdate' => array(
                'method' => 'PUT',
                'url'    => '/users/'.$id,
                'content_type' => 'application/json',
                'url_part' => array('id')
            ),

            'UserDelete' => array(
                'method' => 'DELETE',
                'url'    => '/users/'.$id,
                'content_type' => '',
                'url_part' => array('id')
            ),

            'CategoriesListGet' => array(
                'method' => 'GET',
                'url'    => '/knowledge_base/categories/',
                'content_type' => '',
                'url_part' => array()
            ),

            'ArticlesListGet' => array(
                'method' => 'GET',
                'url'    => '/knowledge_base/articles/',
                'content_type' => '',
                'url_part' => array()
            ),

            'TicketStatusesListGet' => array(
                'method' => 'GET',
                'url'    => '/statuses/',
                'content_type' => '',
                'url_part' => array()
            ),

            'TicketPrioritiesListGet' => array(
                'method' => 'GET',
                'url'    => '/priorities/',
                'content_type' => '',
                'url_part' => array()
            ),

            'TicketTypesListGet' => array(
                'method' => 'GET',
                'url'    => '/types/',
                'content_type' => '',
                'url_part' => array()
            ),

            'CustomFieldsListGet' => array(
                'method' => 'GET',
                'url'    => '/custom_fields/',
                'content_type' => '',
                'url_part' => array()
            ),

            'CustomFieldGet' => array(
                'method' => 'GET',
                'url'    => '/custom_fields/'.$custom_field_id,
                'content_type' => '',
                'url_part' => array('custom_field_id')
            ),

            'OptionsGet' => array(
                'method' => 'GET',
                'url'    => '/custom_fields/'.$custom_field_id.'/options/',
                'content_type' => '',
                'url_part' => array('custom_field_id')
            ),

            'OptionsSet' => array(
                'method' => 'POST',
                'url'    => '/custom_fields/'.$custom_field_id.'/options/',
                'content_type' => 'application/json',
                'url_part' => array('custom_field_id')
            ),

            'OptionsDelete' => array(
                'method' => 'DELETE',
                'url'    => '/custom_fields/'.$custom_field_id.'/options/'.$option_id,
                'content_type' => '',
                'url_part' => array('custom_field_id', 'option_id')
            )
        );

        return $this->request_config[$request_type];
    }

}