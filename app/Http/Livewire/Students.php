<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;

class Students extends Component
{
    use WithPagination;

	protected $paginationTheme = 'bootstrap';
    public $selected_id, $keyWord, $name, $mail, $phone;
    public $updateMode = false;

    public function render()
    {
		$keyWord = '%'.$this->keyWord .'%';
        return view('livewire.students.view', [
            'students' => Student::latest()
						->orWhere('name', 'LIKE', $keyWord)
						->orWhere('mail', 'LIKE', $keyWord)
						->orWhere('phone', 'LIKE', $keyWord)
						->paginate(10),
        ]);
    }
	
    public function cancel()
    {
        $this->resetInput();
        $this->updateMode = false;
    }
	
    private function resetInput()
    {		
		$this->name = null;
		$this->mail = null;
		$this->phone = null;
    }

    public function store()
    {
        $this->validate([
		'name' => 'required',
		'mail' => 'required',
		'phone' => 'required',
        ]);

        Student::create([ 
			'name' => $this-> name,
			'mail' => $this-> mail,
			'phone' => $this-> phone
        ]);
        
        $this->resetInput();
		$this->emit('closeModal');
		session()->flash('message', 'Estudiante creado exitosamente.');
    }

    public function edit($id)
    {
        $record = Student::findOrFail($id);

        $this->selected_id = $id; 
		$this->name = $record-> name;
		$this->mail = $record-> mail;
		$this->phone = $record-> phone;
		
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
		'name' => 'required',
		'mail' => 'required',
		'phone' => 'required',
        ]);

        if ($this->selected_id) {
			$record = Student::find($this->selected_id);
            $record->update([ 
			'name' => $this-> name,
			'mail' => $this-> mail,
			'phone' => $this-> phone
            ]);

            $this->resetInput();
            $this->updateMode = false;
			session()->flash('message', 'Student Successfully updated.');
        }
    }

    public function destroy($id)
    {
        if ($id) {
            $record = Student::where('id', $id);
            $record->delete();
        }
    }
}
