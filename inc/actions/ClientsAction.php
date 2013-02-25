<?php
/**
 * "Clients" action.
 *
 * @author John Resig, 2008-2011
 * @author Jörn Zaefferer, 2012
 * @author Timo Tijhof, 2012-2013
 * @since 0.1.0
 * @package TestSwarm
 */

class ClientsAction extends Action {

	/**
	 * @actionParam string view: One of 'list' or 'grid'
	 * @actionParam sort string: [optional] What to sort the results by.
	 * Must be one of "name", "updated" or "score". Defaults to "updated".
	 * @actionParam sort_dir string: [optional]
	 * Must be one of "asc" (ascending) or "desc" (decending). Defaults to "asc".
	 * @actionParam filter string: [optiona] What filter to apply.
	 * Must be one of "all" or "active". Defaults to "active".
	 * @actionParam string item: Show only information from
	 *  clients by this name, implies view=grid and filter=all.
	 */
	public function doAction() {
		$context = $this->getContext();
		$request = $context->getRequest();

		$view = $request->getVal( 'view', 'list' );
		$item = $request->getVal( 'item' );
		$sortField = $request->getVal( 'sort', 'updated' );
		$sortDir = $request->getVal( 'sort_dir', 'asc' );
		$filter = $request->getVal( 'filter', 'active' );

		if ( !in_array( $sortField, array( 'name', 'updated', 'score' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort `$sortField`." );
			return;
		}

		if ( !in_array( $sortDir, array( 'asc', 'desc' ) ) ) {
			$this->setError( 'invalid-input', "Unknown sort direction `$sortDir`." );
			return;
		}

		if ( !in_array( $filter, array( 'all', 'active' ) ) ) {
			$this->setError( 'invalid-input', "Unknown filter `$filter`." );
			return;
		}

		if ( !in_array( $view, array( 'list', 'grid' ) ) ) {
			$this->setError( 'invalid-input', "Unknown view `$view`." );
			return;
		}

		if ( $item ) {
			$view = 'grid';
			$filter = 'all';
		}

		if ( $view === 'list' ) {
			$this->listView( $sortField, $sortDir, $filter );
		} elseif ( $view === 'grid' ) {
			$this->gridView( $item );
		}
	}

	protected function gridView( $name = null ) {
		$context = $this->getContext();
		$db = $context->getDB();
		$db->getRows(
			'SELECT
				clients.id as client_id,
				clients.name as client_id,
				clients.created as client_id,
				clients.id as client_id,
			'
		);
		//
	}

	protected function listView( $sortField, $sortDir, $filter ) {
		$context = $this->getContext();
		$db = $context->getDB();

		$sortDirQuery = strtoupper( $sortDir );
		$sortFieldQuery = "ORDER BY $sortField $sortDirQuery";

		if ( $filter === 'active' ) {
			$filterQuery = 'AND clients.updated > ' . swarmdb_dateformat( Client::getMaxAge( $context ) );
		} else {
			$filterQuery = '';
		}

		$rows = $db->getRows(
			"SELECT
				clients.name as name,
				clients.updated as updated,
				SUM(runresults.total) as score
			FROM
				clients, runresults
			WHERE clients.id = runresults.client_id
			$filterQuery
			GROUP BY name
			$sortFieldQuery;"
		);

		$scores = array();
		if ( $rows ) {
			foreach ( $rows as $pos => $row ) {
				$scores[] = array(
					'position' => intval( $pos + 1 ), // Array is 0 based
					'name' => $row->name,
					'viewUrl' => swarmpath( "clients/{$row->name}" ),
					'score' => intval( $row->score )
				);
			}
		}

		$this->setData( $scores );
	}
}
