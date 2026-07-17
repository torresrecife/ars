<?php

declare(strict_types=1);

namespace App\ViewModels;

class MainPageViewData
{
	/** @var MainPageUserContext */
	private $user;

	/** @var MainPageState */
	private $state;

	/** @var string */
	private $monthYearLabel;

	/** @var MainPageTopAction|null */
	private $topAction;

	/** @var MainPageContent */
	private $content;

	public function __construct(
		MainPageUserContext $user,
		MainPageState $state,
		$monthYearLabel,
		MainPageContent $content,
		MainPageTopAction $topAction = null
	) {
		$this->user = $user;
		$this->state = $state;
		$this->monthYearLabel = (string) $monthYearLabel;
		$this->content = $content;
		$this->topAction = $topAction;
	}

	public function user()
	{
		return $this->user;
	}

	public function state()
	{
		return $this->state;
	}

	public function currentSection()
	{
		return $this->state->section();
	}

	public function monthYearLabel()
	{
		return $this->monthYearLabel;
	}

	public function topAction()
	{
		return $this->topAction;
	}

	public function canAdmin()
	{
		return $this->user->canAdmin();
	}

	public function content()
	{
		return $this->content;
	}

	public function toArray()
	{
		return array(
			'user' => $this->user->toArray(),
			'state' => $this->state->toArray(),
			'currentSection' => $this->currentSection(),
			'monthYearLabel' => $this->monthYearLabel,
			'topAction' => $this->topAction ? $this->topAction->toArray() : null,
			'canAdmin' => $this->canAdmin(),
			'content' => $this->content->toArray(),
		);
	}
}
