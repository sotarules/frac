<?php
/*
 * Frac
 * Copyright (c) 2009 Frac Development Team
 *
 * See COPYING for license conditions.
 */

class ProjectsController extends Controller
{   
	public function index($args) // list projects
	{
		// well since we're here, that means we should be listing projects.
		// or something.
		$q = Doctrine_Query::create()
			->from('Project p')
			->leftJoin('p.Episodes e');
		$projects = $q->execute();

		for ($i = 0; $i < count($projects); $i++)
		{
			$project = $projects->get($i);
			$arrProject = array(
				'name' => $project->name,
				'description' => $project->description,
				'totaleps' => $project->episodes,
				'trackedeps' => count($project->Episodes->getData())
			);
			$result[$project->id] = $arrProject;
		}
		$this->vars['projects'] = $result;
	}

	// $args is the Project ID. or at least, it should be O_o
	public function display($args) // display the episodes from a project
	{
		$name = Doctrine::getTable("Project")->find($args[0])->name;
		
		$this->vars["name"] = $name;
		$this->vars["pagename"] = "Projects :: " . $name;

		$q = Doctrine_Query::create()
			->select('e.*, t.*')
			->from('Episode e')
			->leftJoin('e.Tasks t')
			->where('e.project = '. $args[0]);

		$episodes = $q->execute();
		foreach ($episodes AS $cur_ep)
		{
			$active = 0;
			$finished = 0;
			foreach ($cur_ep->Tasks AS $cur_task)
			{
				if ($cur_task->active == true) $active++;
				else if ($cur_task->finished == true) $finished++;
			}
			$this->vars['episodes'][] = array(
				'episode' => $cur_ep->episode,
				'airdate' => $cur_ep->airdate,
				'active' => $active,
				'finished' => $finished,
				'open' => count($cur_ep->Tasks) - $finished
			);
		}
	}

	public function create($args) // create a new project
	{
		$p = PermissionHandler::getInstance();
		// do we have an error thing?
		if (!$p->allowedto(PermissionHandler::PERM_CREATE_PROJECT))
		{
			Utils::error("You don't have permission to create projects.");
			return;
		}
		
		if (isset($_POST['go']))
		{
			// ok, if the user has selected to do automatic lookup
			// of the series or automatic adding of episodes,
			// then we need to confirm that we're looking up the right
			// series.
			// scratch that. we should confirm no matter what, but if they chose
			// automatic lookup, do it here.
			$this->vars['tid'] = 0;
			$this->vars['search'] = array();
			if (!isset($_POST['confirm']))
			{
				// implement automatic lookup
				// if the user wants automatic lookup, do it.
				if ($_POST['autolookup'] == "on")
				{
					require_once(dirname(__FILE__) . "/../plugins/animedata.php");
					// fill in the autolookup stuff...
					// first we need to find the anime.
					$search = AnimeData::search($_POST['name']);
					if($search)
					{
						if (!isset($_POST['tid'])) $tidkey = 0;
						else foreach ($search AS $key => $entry) if ($entry[0] == $_POST['tid']) $tidkey = $key;

						$this->vars['tid'] = $search[$tidkey][0];
						$this->vars['search'] = $search;
					
						$_POST['description'] = AnimeData::description($search[$tidkey][0]);
						$epcount = AnimeData::epcount($search[$tidkey][0]);
						$_POST['epsaired'] = $epcount['aired'];
						$_POST['epstotal'] = $epcount['total'];
						$_POST['airtime'] = $epcount['airtime'];
					} else {
						Utils::warning("Cannot autofind anime.");
					}
				}
				$this->vars['confirm'] = $_POST; // LOL LAZY
				// display a confirmation
				$this->view = "confirm";
				return;
			}

			// if they've already confirmed, then go ahead and create the project
			$project = new Project();
			$project->name = $_POST['name'];
			$project->shortname = $_POST['shortname'];
			$project->description = $_POST['description'];
			$project->episodes = $_POST['epstotal'];
			if ($_POST['leader'] != "none")
				$project->leader = $_POST['leader'];
			$project->save();

			// if the user has chosen to automatically add episodes, do so now
			if ($_POST['autoeps'] == "aired")
				for ($i = 1; $i <= $_POST['epsaired']; $i++)
				{
					$episode = new Episode();
					$episode->project = $project->id;
					$episode->episode = $i;
					$episode->save();
				}
			else if ($_POST['autoeps'] == "total")
				for ($i = 1; $i <= $_POST['epstotal']; $i++)
				{
					$episode = new Episode();
					$episode->project = $project->id;
					$episode->episode = $i;
					$episode->save();
				}

			// and finally, send them to the project page.
			Utils::redirect("projects/display/" . $project->id);
			$this->view = null;
			return;
		}

		// otherwise, i don't think we actually need to do anything... right?
	}

	public function delete($args) // delete a project
	{
		$project = $args[0];

		$p = PermissionHandler::getInstance();
		// do we have an error thing?
		if (!$p->allowedto(PermissionHandler::PERM_PROJECT_DELETE, $project))
		{
			Utils::error("You don't have permission to delete this project.");
			return;
		}

	}

	public function edit($args) // edit a project's settings
	{
		$project = $args[0];

		$p = PermissionHandler::getInstance();
		// do we have an error thing?
		if (!$p->allowedto(PermissionHandler::PERM_EDIT_SETTINGS, $project))
		{
			Utils::error("You don't have permission to edit this project.");
			return;
		}

	}
}
