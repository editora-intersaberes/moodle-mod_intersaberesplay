<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Intersaberes Play service class for API communication.
 *
 * @package    mod_intersaberesplay
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_intersaberesplay\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * PlayService class for handling API communication with Intersaberes Play platform.
 *
 * @copyright  2026 Editora Intersaberes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class play_service {
    /** @var string The base URL for the API. */
    private $baseurl;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->baseurl = 'https://play.intersaberes.com';
        if (defined('INTERSABERES_PLAY_URL')) {
            $this->baseurl = INTERSABERES_PLAY_URL;
        }
    }

    /**
     * Access student as demonstration in the library system.
     *
     * @param array $data Parameters to send.
     * @return string API response.
     */
    public function access_via_plugin(array $data): string {
        return $this->request('api/player/rotas', $data);
    }

    /**
     * Get the maximum weight of tests.
     *
     * @param array $data Parameters to send.
     * @return string API response.
     */
    public function get_maximum_test_weight(array $data): string {
        return $this->request('api/player/buscar-peso-maximo', $data);
    }

    /**
     * Send HTTP request to the API.
     *
     * @param string $endpoint The API endpoint.
     * @param array $data The data to send.
     * @return string The API response.
     */
    private function request(string $endpoint, array $data = []): string {
        $url = rtrim($this->baseurl, '/') . '/' . ltrim($endpoint, '/');

        $curl = new \curl();
        $curl->setopt([
            'CURLOPT_TIMEOUT' => 60,
            'CURLOPT_SSL_VERIFYPEER' => strpos($url, 'https://') === 0,
        ]);

        $response = $curl->post($url, $data);

        return $response;
    }
}
