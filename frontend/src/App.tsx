import { useState } from 'react';
import { NumberInput } from './components/NumberInput';
import { ResultDisplay } from './components/ResultDisplay';
import { ErrorDisplay } from './components/ErrorDisplay';
import { CalculateButton } from './components/CalculateButton';

interface SumResponse {
  sum: number;
}
interface ErrorResponse {
  error: string;
}

interface sumElement {
  id: number;
}

function App() {
  const URL = 'http://localhost:8080/api/sum';
  const SHOW_BACKEND_ERRORS = true; // For demonstration, bypassing frontend validation to showcase backend error handling
  const [a, setA] = useState<string>('');
  const [b, setB] = useState<string>('');
  const [result, setResult] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(false);

  const resetStates = () => {
    setResult(null);
    setError(null);
  }

  const isValidInput = (a: string, b: string): boolean => {
    if (a === '' || b === '') return false;
    
    const numA = parseFloat(a);
    const numB = parseFloat(b);
    
    return !isNaN(numA) && !isNaN(numB);
  }

  const handleCalculate = async () => {
    resetStates();
    
    if(!SHOW_BACKEND_ERRORS){
      if (!isValidInput(a, b)) {
        if (a === '' || b === '') {
          setError('Both inputs are required');
        } else {
          setError('Both inputs must be valid numbers');
        }
        return;
      } 
    }

    setLoading(true);  

    try {      
      const response = await fetch(URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          a: parseFloat(a),
          b: parseFloat(b),
        }),
      });
      
      // const data: SumResponse | ErrorResponse = await response.json();
      const data = await response.json();

      if (response.ok) {
        // const new_data = {...data.pop()} as SumResponse;
        const new_data = data.filter( (e : sumElement) => e.id == 5);

        setResult((new_data as SumResponse).sum);
      } else {
        setError((data as ErrorResponse).error || 'An error occurred');
      }
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'An unexpected error occurred';
      setError(message);
    } finally {
      setLoading(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter' && isValidInput(a, b) && !loading) {
      handleCalculate();
    }
  };
  
  const isValid = SHOW_BACKEND_ERRORS ? true : isValidInput(a, b);

  return (
    <div className="min-h-screen bg-linear-to-br from-indigo-500 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="bg-white rounded-2xl shadow-2xl p-8">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-800 mb-2">
              Sum Calculator
            </h1>
            <p className="text-gray-600 text-sm">
              Enter two numbers to calculate their sum
            </p>
          </div>

          <div className="space-y-4 mb-6">
            <NumberInput
              id="input-a"
              label="Number A"
              value={a}
              onChange={setA}
              onKeyDown={handleKeyDown}
              placeholder="Enter first number"
              disabled={loading}
            />

            <NumberInput
              id="input-b"
              label="Number B"
              value={b}
              onChange={setB}
              onKeyDown={handleKeyDown}
              placeholder="Enter second number"
              disabled={loading}
            />
          </div>

          <CalculateButton
            onClick={handleCalculate}
            disabled={loading || !isValid}
            loading={loading}
          />

          <ResultDisplay result={result} />

          <ErrorDisplay error={error} />
        </div>

        <div className="text-center mt-6">
          <p className="text-white text-sm">
            Developer: Gad Diego Andres Sanchez Ortega
          </p>
        </div>
      </div>
    </div>
  );
}

export default App;
