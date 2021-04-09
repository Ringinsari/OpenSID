<?php
require_once 'vendor/google-api-php-client/vendor/autoload.php';

class Analisis_import_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('penduduk_model');
		$this->load->model('keluarga_model');
		$this->load->library('Spreadsheet_Excel_Reader');
	}

	public function import_excel($file='', $kode='00000', $jenis=2)
	{
		if (empty($file)) $file = $_FILES['userfile']['tmp_name'];
		$data = new Spreadsheet_Excel_Reader($file);
		$sheet=0;

		$master['nama']	= $data->val(1, 2, $sheet);
		$master['subjek_tipe'] = $data->val(2, 2, $sheet);
		$master['lock']	= $data->val(3, 2, $sheet);
		$master['pembagi'] = $data->val(4, 2, $sheet);
		$master['deskripsi'] = $data->val(5, 2, $sheet);
		$master['kode_analisis'] = $kode;
		$master['jenis'] = $jenis;

		$outp = $this->db->insert('analisis_master',$master);
		$id_master = $this->db->insert_id();

		$periode['id_master']	= $id_master;
		$periode['nama'] = $data->val(6, 2, $sheet);
		$periode['tahun_pelaksanaan']	= $data->val(7, 2, $sheet);
		$periode['keterangan'] = $data->val(5, 2, $sheet);
		$periode['aktif']	= 1;
		$this->db->insert('analisis_periode', $periode);

		$sheet = 1;
		$baris = $data->rowcount($sheet_index=$sheet);
		$kolom = $data->colcount($sheet_index=$sheet);

		for ($i=2; $i<=$baris; $i++)
		{
			$sql = "SELECT * FROM analisis_kategori_indikator WHERE kategori=? AND id_master=?";
			$query = $this->db->query($sql, array($data->val($i, 3, $sheet), $id_master));
			$cek = $query->row_array();

			if (!$cek)
			{
				$kategori['id_master'] = $id_master;
				$kategori['kategori']	= $data->val($i, 3, $sheet);
				$this->db->insert('analisis_kategori_indikator', $kategori);
			}
		}

		for ($i=2; $i<=$baris; $i++)
		{
			$indikator['id_master']	= $id_master;
			$indikator['nomor']	= $data->val($i, 1, $sheet);
			$indikator['pertanyaan'] = $data->val($i, 2, $sheet);

			$sql = "SELECT * FROM analisis_kategori_indikator WHERE kategori=? AND id_master=?";
			$query = $this->db->query($sql, array($data->val($i, 3, $sheet), $id_master));
			$kategori = $query->row_array();

			$indikator['id_kategori']	= $kategori['id'];
			$indikator['id_tipe']	= $data->val($i, 4, $sheet);
			$indikator['bobot']	= $data->val($i, 5, $sheet) ?: 0;
			$indikator['act_analisis'] = $data->val($i, 6, $sheet) ?: 2;

			$this->db->insert('analisis_indikator', $indikator);
		}

		$sheet = 2;
		$baris = $data->rowcount($sheet_index=$sheet);
		$kolom = $data->colcount($sheet_index=$sheet);

		for ($i=2; $i<=$baris; $i++)
		{
			$kode	= explode(".", $data->val($i, 3, $sheet));

			$parameter['kode_jawaban'] = $data->val($i, 2, $sheet);
			$parameter['jawaban']	= $data->val($i, 3, $sheet);

			$sql = "SELECT id FROM analisis_indikator WHERE nomor=? AND id_master=?";
			$query = $this->db->query($sql, array($data->val($i, 1, $sheet), $id_master));
			$indikator = $query->row_array();

			$parameter['id_indikator'] = $indikator['id'];
			$parameter['nilai']	= $data->val($i, 4, $sheet) ?: 0;
			$parameter['asign']	= 1;

			$this->db->insert('analisis_parameter',$parameter);
		}

		$sheet = 3;
		$baris = $data->rowcount($sheet_index=$sheet);
		$kolom = $data->colcount($sheet_index=$sheet);

		for ($i=2; $i<=$baris; $i++)
		{
			$klasifikasi['id_master']	= $id_master;
			$klasifikasi['nama'] = $data->val($i, 1, $sheet);
			$klasifikasi['minval'] = $data->val($i, 2, $sheet);
			$klasifikasi['maxval'] = $data->val($i, 3, $sheet);

			$this->db->insert('analisis_klasifikasi', $klasifikasi);
		}

		status_sukses($outp); //Tampilkan Pesan

		return $id_master;
	}

	public function save_import_gform(){
		$list_error = array();

		// SIMPAN ANALISIS MASTER
		$data_analisis_master = [
			'nama' 			=> $this->input->post('nama_form') == "" ? "Response Google Form " . date('dmY_His') : $this->input->post('nama_form'),
			'subjek_tipe' 	=> $this->input->post('subjek_analisis') == 0 ? 1 : $this->input->post('subjek_analisis'),
			'id_kelompok' 	=> 0,
			'lock' 			=> 1,
			'format_impor' 	=> 0,
			'pembagi' 		=> 1,
			'id_child' 		=> 0,
			'deskripsi' 	=> ""
		];

		$outp = $this->db->insert('analisis_master', $data_analisis_master);
		$id_master = $this->db->insert_id();

		// SIMPAN KATEGORI ANALISIS
		$list_kategori = $this->input->post('kategori');
		$temp_unique_kategori = array();
		$list_unique_kategori = array();

		// Get Unique Value dari Kategori
		foreach ($list_kategori as $key => $val)
		{
			if($this->input->post('is_selected')[$key] == 'true')
			{
				if(! in_array($val, $temp_unique_kategori))
				{
					array_push($temp_unique_kategori, $val);
				}
			}
		}
		
		// Simpan Unique Value dari Kategori
		foreach ($temp_unique_kategori as $key => $val)
		{
			$data_kategori = [
				'id_master'		=> $id_master,
				'kategori' 		=> $val,
				'kategori_kode'	=> ""
			];

			$outp = $this->db->insert('analisis_kategori_indikator', $data_kategori);
			$id_kategori = $this->db->insert_id();

			$list_unique_kategori[$id_kategori] = $val;
		}

		// SIMPAN PERTANYAAN/INDIKATOR ANALISIS
		$id_column_nik_kk = $this->input->post('id-row-nik-kk');
		$count_indikator = 1;
		$db_idx_parameter = array();
		$db_idx_indikator = array();
		foreach ($this->input->post('pertanyaan') as $key => $val)
		{
			$temp_idx_parameter = array();
			$id_indikator = 0;
			if($this->input->post('is_selected')[$key] == 'true' && $key != $id_column_nik_kk)
			{
				$data_indikator = [
					'id_master'		=> $id_master,
					'nomor'			=> $count_indikator,
					'pertanyaan' 	=> $val,
					'id_tipe' 		=> $this->input->post('tipe')[$key],
					'bobot' 		=> $this->input->post('bobot')[$key],
					'act_analisis' 	=> 0,
					'id_kategori' 	=> array_search($this->input->post('kategori')[$key], $list_unique_kategori),
					'is_publik' 	=> 0,
					'is_teks' 		=> 0
				];

				if($data_indikator['id_tipe'] != 1)
				{
					$data_indikator['act_analisis']	= 2;
					$data_indikator['bobot'] 		= 0;
				}
	
				$outp = $this->db->insert('analisis_indikator', $data_indikator);
				$id_indikator = $this->db->insert_id();

				// Simpan Parameter untuk setiap unique value pada masing-masing indikator
				foreach ($this->input->post('unique-param-value-' . $key) as $param_key => $param_val)
				{
					$data_parameter = [
						'id_indikator'	=> $id_indikator,
						'jawaban'		=> $this->input->post('unique-param-value-' . $key)[$param_key],
						'nilai' 		=> $this->input->post('unique-param-nilai-' . $key)[$param_key],
						'kode_jawaban' 	=> ($param_key+1),
						'asign' 		=> 0
					];

					$outp = $this->db->insert('analisis_parameter', $data_parameter);
					$id_parameter = $this->db->insert_id();
					$temp_idx_parameter[$id_parameter] = $param_val;
				}
				
				$count_indikator += 1;
			}
			$db_idx_indikator[$id_indikator] = $key;
			array_push($db_idx_parameter, $temp_idx_parameter);
		}

		// SIMPAN PERIODE ANALISIS
		$data_periode = [
			'id_master' 		=> $id_master,
			'nama' 				=> "Pendataan " . date('dmY_His'),
			'id_state' 			=> 1,
			'aktif' 			=> 1,
			'keterangan' 		=> 0,
			'tahun_pelaksanaan'	=> $this->input->post('tahun_pendataan') == "" ? date('Y') : $this->input->post('tahun_pendataan')
		];

		$outp = $this->db->insert('analisis_periode', $data_periode);
		$id_periode = $this->db->insert_id();

		// SIMPAN RESPON ANALISIS
		$data_import = $this->session->data_import;
		// Iterasi untuk setiap subjek
		foreach ($data_import['jawaban'] as $key_jawaban => $val_jawaban)
		{
			// Get Id Subjek berdasarkan Tipe Subjek (Penduduk / Keluarga / Rumah Tangga / Kelompok)
			$nik_kk_subject = $val_jawaban[$id_column_nik_kk];
			if($data_analisis_master['subjek_tipe'] == 2)
				$id_subject = $this->keluarga_model->get_keluarga_by_no_kk($nik_kk_subject)['id'];
			else
				$id_subject = $this->penduduk_model->get_penduduk_by_nik($nik_kk_subject)['id'];
			
			if($id_subject != NULL && $id_subject != "")
			{
				// Iterasi untuk setiap indikator / jawaban dari subjek
				foreach ($this->input->post('pertanyaan') as $key_pertanyaan => $val_pertanyaan)
				{
					if($this->input->post('is_selected')[$key_pertanyaan] == 'true' && $key_pertanyaan != $id_column_nik_kk)
					{
						$data_respon = [
							'id_indikator'	=> array_search($key_pertanyaan, $db_idx_indikator),
							'id_parameter'	=> array_search($val_jawaban[$key_pertanyaan], $db_idx_parameter[$key_pertanyaan]),
							'id_subjek' 	=> $id_subject,
							'id_periode' 	=> $id_periode
						];

						$outp = $this->db->insert('analisis_respon', $data_respon);
					}
				}
			}
			else
			{
				array_push($list_error, 'NIK / No. KK data ke-' . ($key_jawaban+1) . " (" . $nik_kk_subject . ") " . $id_subject . " tidak valid");
			}
		}

		$this->session->list_error = $list_error;
		status_sukses($outp);
	}

	function getOAuthCredentialsFile()
	{
		// Location of Oauth2 Credential
		$oauth_creds = APPPATH . '../vendor/google-api-php-client/oauth-credentials.json';

		if (file_exists($oauth_creds)) 
		{
			return $oauth_creds;
		}

		return false;
	}

	public function import_gform($redirect_link = ""){
		// Check Credential File
		if (!$oauth_credentials = $this->getOAuthCredentialsFile()) 
		{
			echo 'ERROR - File Credential Not Found';
			return;
		}

		$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

		// Get the API client and construct the service object.
		$client = new Google\Client();
		$client->setAuthConfig($oauth_credentials);
		$client->setRedirectUri($redirect_uri);
		$client->addScope("https://www.googleapis.com/auth/forms");
		$client->addScope("https://www.googleapis.com/auth/spreadsheets");
		$service = new Google_Service_Script($client);

		// API script id
		$scriptId = 'AKfycbx3KRsQ_OsDpq4r2bWmW-BaOUaQzktkavrCBjpKHpw-KNN4GHho6_g6leY43ueKwpc6OQ';

		// add "?logout" to the URL to remove a token from the session
		if (isset($_REQUEST['logout'])) 
			unset($_SESSION['upload_token']);

		if (isset($_GET['code'])) {
			$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
			$client->setAccessToken($token);
			
			// store in the session also
			$_SESSION['upload_token'] = $token;

			// // redirect back to the example
			// header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
		}

		// set the access token as part of the client
		if (!empty($_SESSION['upload_token'])) 
		{
			$client->setAccessToken($_SESSION['upload_token']);
			if ($client->isAccessTokenExpired())
				unset($_SESSION['upload_token']);
		} else 
			$authUrl = $client->createAuthUrl();

		// Create an execution request object.
		$request = new Google_Service_Script_ExecutionRequest();
		$request->setFunction('getFormItems');
		$form_id = $this->session->google_form_id;
		if ($form_id == "")
			$form_id = $this->session->gform_id;
		$request->setParameters($form_id);

		try 
		{
			if (isset($authUrl) && $_SESSION['inside_retry'] != true)
			{	
				// If no authentication before
				$this->session->gform_id = $form_id;
				$this->session->inside_retry = true;
				$this->session->inside_redirect_link = $redirect_link;
				header('Location: ' . $authUrl);
			} 
			else 
			{
				// If it has authenticated
				// Make the API request.
				$response = $service->scripts->run($scriptId, $request);

				if ($response->getError()) 
				{
					echo 'Error';
					// The API executed, but the script returned an error.

					// Extract the first (and only) set of error details. The values of this
					// object are the script's 'errorMessage' and 'errorType', and an array of
					// stack trace elements.
					$error = $response->getError()['details'][0];
					printf("Script error message: %s\n", $error['errorMessage']);

					if (array_key_exists('scriptStackTraceElements', $error)) 
					{
						// There may not be a stacktrace if the script didn't start executing.
						print "Script error stacktrace:\n";
						foreach($error['scriptStackTraceElements'] as $trace) 
							printf("\t%s: %d\n", $trace['function'], $trace['lineNumber']);
					}
				} 
				else 
				{
					// Get Response
					$resp = $response->getResponse();
					return $resp['result'];
				}
			}
			
		} catch (Exception $e) 
		{
			// The API encountered a problem before the script started executing.
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}

		return '0';
	}
}
