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
 * Brazilian Portuguese language strings.
 *
 * @package   local_accessibilityai
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Auditoria de acessibilidade com IA';
$string['navigationlink'] = 'Auditoria de acessibilidade';
$string['accessibilityai:audit'] = 'Executar auditorias de acessibilidade nos conteúdos do curso';
$string['runaudit'] = 'Executar auditoria de acessibilidade';
$string['certificationnotice'] = 'Este relatório é uma ferramenta de assistência, não uma certificação de acessibilidade nem uma auditoria WCAG completa. Findings e sugestões de IA exigem revisão humana.';
$string['reportsummary'] = 'Resumo da auditoria';
$string['itemschecked'] = 'Itens verificados';
$string['errors'] = 'Erros';
$string['warnings'] = 'Avisos';
$string['aisuggestions'] = 'Sugestões de IA';
$string['itemswithoutfindings'] = 'Itens sem findings detectados';
$string['editcontent'] = 'Editar conteúdo';
$string['element'] = 'Elemento';
$string['reason'] = 'Motivo';
$string['suggestion'] = 'Sugestão';
$string['location'] = 'Localização';
$string['rule'] = 'Regra';
$string['category'] = 'Categoria';
$string['nofindings'] = 'Nenhum finding determinístico nem sugestão de IA foi retornado para este item. Isso não significa que o item esteja em conformidade com WCAG nem que seja plenamente acessível.';
$string['noitems'] = 'Nenhum conteúdo autoral suportado foi encontrado neste curso.';
$string['aiwarningtitle'] = 'A revisão por IA ficou incompleta';
$string['bridgeclassmissing'] = 'A classe de API obrigatória do local_ai_bridge não está disponível. As verificações determinísticas locais foram concluídas normalmente.';
$string['aibatchfailed'] = 'O lote {$a} da revisão por IA não pôde ser concluído porque o AI Bridge, tenant, purpose, rota ou provider estava indisponível. As verificações determinísticas locais foram concluídas normalmente.';
$string['privacy:metadata'] = 'O local_accessibilityai não persiste dados pessoais nem conteúdo do curso. Quando um professor executa a auditoria, conteúdo autoral minimizado do curso pode ser enviado pelo local_ai_bridge conforme a configuração daquele plugin e do provider. Submissões de alunos não são analisadas nesta versão.';
$string['source_section'] = 'Seção do curso';
$string['source_page'] = 'Página';
$string['source_bookintro'] = 'Descrição do Livro';
$string['source_bookchapter'] = 'Capítulo do Livro';
$string['source_label'] = 'Área de texto e mídia';
$string['source_assignment'] = 'Descrição da Tarefa';
$string['source_forum'] = 'Descrição do Fórum';
$string['rule_img_missing_alt_reason'] = 'A imagem não possui atributo alt, portanto não é possível determinar pelo HTML uma alternativa textual.';
$string['rule_img_missing_alt_suggestion'] = 'Adicione um alt significativo para imagens informativas ou alt="" quando a imagem for intencionalmente decorativa.';
$string['rule_img_empty_alt_reason'] = 'A imagem possui alt="", mas não há sinal local no HTML indicando que ela foi intencionalmente marcada como decorativa.';
$string['rule_img_empty_alt_suggestion'] = 'Confirme se a imagem é decorativa. Se transmitir informação, forneça uma alternativa textual curta; caso contrário, mantenha alt="" e deixe a intenção decorativa clara quando fizer sentido.';
$string['rule_heading_skip_reason'] = 'A sequência de headings salta de h{$a->from} para h{$a->to}, o que pode dificultar a navegação pela estrutura do documento.';
$string['rule_heading_skip_suggestion'] = 'Use níveis de heading para representar hierarquia e não pule níveis apenas para obter outro tamanho visual.';
$string['rule_heading_empty_reason'] = 'Um heading vazio cria uma parada de navegação sem significado para tecnologias assistivas.';
$string['rule_heading_empty_suggestion'] = 'Remova o heading vazio ou forneça um texto que descreva a seção iniciada por ele.';
$string['rule_heading_presentation_reason'] = 'Este heading possui sinais de estilização visual que podem indicar uso do elemento principalmente para aparência. É uma heurística e precisa de revisão.';
$string['rule_heading_presentation_suggestion'] = 'Confirme se o elemento representa uma seção real. Se a intenção for apenas visual, use um elemento não-heading e CSS.';
$string['rule_link_empty_reason'] = 'O link não possui texto visível ou nome acessível significativo detectável localmente.';
$string['rule_link_empty_suggestion'] = 'Forneça texto descritivo ou um rótulo acessível que comunique o destino ou a ação do link.';
$string['rule_link_generic_reason'] = 'Texto genérico como “clique aqui” ou “mais” depende do contexto ao redor e perde sentido quando os links são navegados isoladamente.';
$string['rule_link_generic_suggestion'] = 'Use texto que descreva o destino ou a ação sem depender da frase ao redor.';
$string['rule_table_headers_reason'] = 'A tabela não possui células de cabeçalho. Se ela contiver dados tabulares, as relações entre linhas e colunas podem ficar pouco claras para tecnologias assistivas.';
$string['rule_table_headers_suggestion'] = 'Em tabelas de dados, use células th e relações adequadas. Se for apenas layout, evite tabela para posicionamento quando possível ou marque-a explicitamente como apresentacional.';
$string['rule_table_relationship_reason'] = 'A tabela contém cabeçalhos, mas ao menos um deles não possui relação explícita por scope ou id.';
$string['rule_table_relationship_suggestion'] = 'Use scope="col" ou scope="row" em tabelas simples e relações id/headers quando a estrutura for mais complexa.';
$string['rule_obsolete_html_reason'] = 'O elemento {$a} é obsoleto ou problemático para uma marcação semântica e sustentável.';
$string['rule_obsolete_html_suggestion'] = 'Substitua HTML apresentacional por elementos semânticos e CSS.';
$string['rule_positive_tabindex_reason'] = 'Um tabindex positivo força uma ordem de teclado customizada que pode divergir da ordem visual e do documento.';
$string['rule_positive_tabindex_suggestion'] = 'Prefira a ordem natural do DOM e use tabindex="0" ou tabindex="-1" somente quando o comportamento específico for necessário.';
$string['rule_duplicate_id_reason'] = 'O mesmo id HTML aparece mais de uma vez neste fragmento, o que pode quebrar relações de labels e ARIA.';
$string['rule_duplicate_id_suggestion'] = 'Garanta que cada id seja único na página renderizada e atualize as referências correspondentes.';
$string['rule_video_caption_reason'] = 'Foi detectado vídeo, mas o HTML armazenado não apresenta track de legenda/subtítulo nem indicação próxima de legenda ou transcrição.';
$string['rule_video_caption_suggestion'] = 'Verifique se o vídeo possui legendas precisas e, quando apropriado, transcrição. O player pode oferecer recursos que não aparecem no HTML armazenado.';
$string['rule_audio_transcript_reason'] = 'Foi detectado áudio, mas não foi encontrada indicação próxima de transcrição no conteúdo armazenado.';
$string['rule_audio_transcript_suggestion'] = 'Forneça ou vincule uma transcrição precisa e confirme que ela cobre o conteúdo sonoro relevante.';
$string['rule_embedded_media_reason'] = 'Foi detectado vídeo incorporado, mas não foi encontrada indicação próxima de legenda ou transcrição no conteúdo armazenado.';
$string['rule_embedded_media_suggestion'] = 'Verifique as legendas no player externo e forneça transcrição quando apropriado. Esta regra não consegue inspecionar o player remoto.';
$string['rule_iframe_title_reason'] = 'O iframe não possui atributo title preenchido, portanto sua finalidade pode não ser anunciada claramente.';
$string['rule_iframe_title_suggestion'] = 'Adicione um title curto que descreva o conteúdo incorporado ou sua finalidade.';
$string['rule_contrast_reason'] = 'As cores inline explícitas de primeiro plano e fundo produzem razão aproximada de contraste {$a}:1. O cálculo local não considera CSS herdado, regras do tema, imagens, transparência nem a renderização final.';
$string['rule_contrast_suggestion'] = 'Revise as cores na página renderizada e ajuste se necessário. Para contraste real, use uma ferramenta de acessibilidade no navegador que leia os estilos finais.';
