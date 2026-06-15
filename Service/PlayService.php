<?php

class PlayService
{
    private $url;

    public function __construct()
    {
        $this->url   = 'https://play.intersaberes.com';
        if (defined('INTERSABERES_PLAY_URL')) {
            $this->url   = INTERSABERES_PLAY_URL;
        }
    }

    /**
     * Acessar aluno como demostração no sistema do biblioteca.
     *
     * @param array $data Parametros
     * @return string
     */
    public function acessarViaPlugin(array $data)
    {
        return $this->request('api/player/rotas', $data);
    }

    public function getPesoMaximoProvas(array $data)
    {
        return $this->request('api/player/buscar-peso-maximo', $data);
    }



    /**
     * Enviar requisições
     *
     * @param string $endpoit
     * @param array|stdClass $data
     * @return null|object|array|string
     */
    private function request($endpoit, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, trim($this->url, '/') . '/' . trim($endpoit, '/'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resposta = curl_exec($ch);
        curl_close($ch);

        return $resposta;
    }
}
