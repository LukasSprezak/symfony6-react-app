import React, {useEffect} from "react";
import { NavigateFunction, useNavigate } from "react-router-dom";
import axios from "axios";
import {Button, Table} from "react-bootstrap";

const Product: React.FC = () => {
    const navigate: NavigateFunction = useNavigate();
    const defaultProducts:Product[] = [];
    const [products, setProducts]: [Product[], (products: Product[]) => void] = React.useState(defaultProducts);
    const [error, setError]: [string, (error: string) => void] = React.useState('');

    useEffect(() => {
        axios
            .get<Product[]|any>('/api/products')
            .then(response => {
                setProducts(response.data['hydra:member']);
            })
            .catch(exception => {
                const error =
                    exception.response.status === 404
                        ? "Resource Not found"
                        : "An unexpected error has occurred";
                setError(error);
            });
    }, []);

    return (
        <Table striped bordered hover>
        <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody className="products">
        {products.map((product: Product) => (
        <tr key={product.id}>
            <td></td>
            <td>{product.name}</td>
            <td>{product.description}</td>
        </tr>
        ))}
        </tbody>

        {error && <p className="error">{error}</p>}
        <Button
            className="btn btn-primary btn-lg mx-3 px-5 py-3 mt-2"
            onClick={() => navigate(-1)}
        >
            Go Back
        </Button>
    </Table>
    );
}

export default Product;
