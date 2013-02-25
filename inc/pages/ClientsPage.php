<?php
/**
 * "Clients" page.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @since 0.1.0
 * @package TestSwarm
 */

class ClientsPage extends Page {

	public function execute() {
		$action = ClientsAction::newFromContext( $this->getContext() );
		$action->doAction();

		$this->setAction( $action );
		$this->content = $this->initContent();
	}

	protected function initContent() {
		$this->setTitle( "Clients" );
		$scores = $this->getAction()->getData();

		$html = '<blockquote><p>All clients grouped by their name.'
		 . ' The score is the number of tests run by those clients.</p></blockquote>'
		 . '<table class="table table-striped">'
		 . '<thead><tr><th class="span1">#</th><th>User</th><th class="span2">Score</th></tr></thead>'
		 . '<tbody>';

		foreach ( $scores as $item ) {
			$html .= '<tr><td class="num">' . htmlspecialchars( number_format( $item["position"] ) ) . '</td>'
				. '<td><a href="' . htmlspecialchars( $item['viewUrl'] ) . '">' . htmlspecialchars( $item["name"] ) . '</a></td>'
				. '<td class="num">' . htmlspecialchars( number_format( $item["score"] ) ) . '</td></tr>';
		}
		$html .= '</tbody></table>';

		return $html;
	}

}
