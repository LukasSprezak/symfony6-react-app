import React, { useEffect } from "react";
import { NavigateFunction, useNavigate } from "react-router-dom";
import { Button, Table } from "react-bootstrap";
import ProductInterface from "../../interface/product/ProductInterface";
import authFetch from "../../infrastructure/axios/axios";

const Product: React.FC = () => {
    const navigate: NavigateFunction = useNavigate();
    const defaultProducts:ProductInterface[] = [];
    const [products, setProducts]: [ProductInterface[], (products: ProductInterface[]) => void] = React.useState(defaultProducts);
    const [error, setError]: [string, (error: string) => void] = React.useState('');

    useEffect((): void => {
        authFetch
            .get<ProductInterface[]|any>('/api/products')
            .then(response => {
                setProducts(response.data['hydra:member']);
            })
            .catch(exception => {
                const error =
                    404 === exception.response.status
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
        {products.map((product: ProductInterface) => (
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
