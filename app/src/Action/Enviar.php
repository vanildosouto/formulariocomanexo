<?php

namespace App\Action;

use Psr\Log\LoggerInterface;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \PHPMailer;

final class Enviar
{
    private $logger;
    private $email;
    private $view;

    public function __construct(LoggerInterface $logger, Twig $view, PHPMailer $email)
    {
        $this->logger = $logger;
        $this->view   = $view;
        $this->email  = $email;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $dados = $request->getParsedBody();
            $arquivos = $request->getUploadedFiles();

            if (empty($arquivos['receita'])) {
                throw new \Exception("É necessário enviar a receita");
            }

            $receita = $arquivos['receita'];

            $tipos_permitidos = [
                'image/jpeg',
                'image/png',
                'application/pdf',
            ];

            if (!in_array($receita->getClientMediaType(), $tipos_permitidos)) {
                $this->logger->debug("Tentativa de arquivo inválido", [
                    'tipo de arquivo' => $receita->getClientMediaType(),
                ]);

                throw new \Exception('Tipo de arquivo não é permitido. Tipos permitidos são: png, jpg e pdf');
            }

            $mimetype = explode("/", $receita->getClientMediaType());
            $ext = end($mimetype);

            $nome_temporario = tempnam('/tmp', 'EMAIL') . '.' . $ext;

            $receita->moveTo($nome_temporario);

            $this->email->Body = '<h2>Novo pedido de orçamento</h2>';
            $this->email->Body .= "<p>Cliente:" . $dados['nome'] . "</p>";
            $this->email->Body .= "<p>Email:" . $dados['email'] . "</p>";
            $this->email->addAttachment($nome_temporario);

            if ($this->email->send()) {
                $this->view->render($response, 'ok.html', []);
            } else {
                $error = $this->email->ErrorInfo;
                $this->logger->error("Ocorreu um erro ao enviar o email", ["erro" => $error]);
                throw new \Exception("Não foi possível enviar o email");
            }
        } catch (\Exception $exp) {
            $this->logger->error("Ocorreu um erro", [
                'message' => $exp->getMessage(),
                'code' => $exp->getCode(),
                'line' => $exp->getLine(),
                'file' => $exp->getFile(),
            ]);
            $this->view->render($response, 'erro.html', [
                'message' => $exp->getMessage(),
            ]);
        }

        return $response;
    }
}
